<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RedeemGateway;
use App\Models\RedeemMethod;
use App\Models\RedeemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{
    public function gateways()
    {
        $gateways = RedeemGateway::where('is_active', true)->orderBy('priority', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $gateways
        ]);
    }

    public function methods($gatewayId)
    {
        $methods = RedeemMethod::where('redeem_gateway_id', $gatewayId)
            ->where('is_active', true)
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $methods
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_id' => 'required|exists:redeem_methods,id',
            'account_details' => 'required|string',
        ]);

        $user = $request->user();
        $method = RedeemMethod::with('gateway')->find($request->method_id);

        if (!$method->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'This redeem method is currently unavailable.'
            ], 400);
        }

        if ($user->coins < $method->coin_cost) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient coins.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Deduct coins
            $user->coins -= $method->coin_cost;
            $user->save();

            // Create Request
            $redeemRequest = RedeemRequest::create([
                'user_id' => $user->id,
                'method_id' => $method->id,
                'gateway_name' => $method->gateway->name,
                'coin_cost' => $method->coin_cost,
                'amount' => $method->amount,
                'currency' => $method->currency,
                'account_details' => $request->account_details,
                'status' => 'pending',
            ]);

            // Record Transaction
            \App\Models\TransactionHistory::create([
                'user_id' => $user->id,
                'type' => 'coin',
                'amount' => -$method->coin_cost,
                'source' => 'redeem_request',
                'description' => 'Redeem Request via ' . $method->gateway->name,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Redeem request submitted successfully!',
                'data' => $redeemRequest,
                'new_balance' => $user->coins
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $history = RedeemRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $history
        ]);
    }
}

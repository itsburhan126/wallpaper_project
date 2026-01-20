<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionHistory;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = TransactionHistory::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $transactions
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TransactionHistory;
use App\Models\AdWatchLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    public function getCategories()
    {
        $categories = \App\Models\Category::where('status', true)->get();
        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    public function getGames(Request $request)
    {
        $query = \App\Models\Game::where('is_active', true);
        
        if ($request->has('featured') && $request->featured == 1) {
            $query->where('is_featured', true);
        }

        $games = $query->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $games
        ]);
    }

    public function getGame($id)
    {
        $game = \App\Models\Game::where('is_active', true)->find($id);
        
        if (!$game) {
            return response()->json([
                'status' => false,
                'message' => 'Game not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $game
        ]);
    }

    public function logAdWatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'status' => 'required|string',
            'ad_type' => 'nullable|string',
            'reward_type' => 'nullable|string',
            'reward_amount' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            AdWatchLog::create([
                'user_id' => $request->user()->id,
                'provider' => $request->provider,
                'status' => $request->status,
                'ad_type' => $request->ad_type ?? 'rewarded',
                'reward_type' => $request->reward_type,
                'reward_amount' => $request->reward_amount ?? 0,
            ]);

            return response()->json(['status' => true, 'message' => 'Ad log saved']);
        } catch (\Exception $e) {
            Log::error('Failed to log ad watch', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Failed to save log'], 500);
        }
    }

    public function updateBalance(Request $request)
    {
        Log::info('GameController: updateBalance called', $request->all());

        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:0',
            'type' => 'required|in:coin,gem',
            'source' => 'required|string', // level_complete, ad_watch, etc.
            'description' => 'nullable|string',
            'level' => 'nullable|integer', // Optional, to update user level
        ]);

        if ($validator->fails()) {
            Log::error('GameController: Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        Log::info('GameController: User found', ['user_id' => $user->id, 'current_coins' => $user->coins]);
        
        DB::beginTransaction();

        try {
            // Update User Balance
            if ($request->type === 'coin') {
                $user->coins += $request->amount;
            } else {
                $user->gems += $request->amount;
            }

            // Update Level if provided and greater than current
            if ($request->filled('level') && $request->level > $user->level) {
                $user->level = $request->level;
            }

            $user->save();
            Log::info('GameController: User balance updated', ['new_coins' => $user->coins, 'new_gems' => $user->gems, 'new_level' => $user->level]);

            // Record Transaction
            TransactionHistory::create([
                'user_id' => $user->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'source' => $request->source,
                'description' => $request->description ?? ucfirst($request->source),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Balance updated successfully',
                'data' => [
                    'coins' => $user->coins,
                    'gems' => $user->gems,
                    'level' => $user->level,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GameController: Transaction failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to update balance: ' . $e->getMessage()
            ], 500);
        }
    }
}

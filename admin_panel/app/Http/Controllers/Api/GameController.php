<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TransactionHistory;
use App\Models\AdWatchLog;
use App\Models\GameHistory;
use App\Models\AdHistory;
use App\Models\LuckyWheelHistory;
use App\Models\Setting;
use App\Helpers\FilePath;
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

    public function incrementPlayCount(Request $request)
    {
        $user = $request->user();
        $today = \Carbon\Carbon::today()->toDateString();

        // Check if last game date is not today, reset count
        if ($user->last_game_date !== $today) {
            $user->daily_game_count = 0;
            $user->last_game_date = $today;
        }

        // Increment count
        $user->daily_game_count += 1;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Game play count incremented',
            'data' => [
                'daily_game_count' => $user->daily_game_count,
                'last_game_date' => $user->last_game_date,
            ]
        ]);
    }

    public function getStatus(Request $request)
    {
        $user = $request->user();
        $today = \Carbon\Carbon::today();
        
        $gamesPlayedToday = \App\Models\GameHistory::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();
            
        $dailyLimit = (int) \App\Models\Setting::get('game_daily_limit', 10);
        
        // Ad Status
        $adsWatchedToday = \App\Models\AdHistory::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();
        $adDailyLimit = (int) \App\Models\Setting::get('ad_daily_limit', 10);

        return response()->json([
            'status' => true,
            'data' => [
                'games_played_today' => $gamesPlayedToday,
                'daily_limit' => $dailyLimit,
                'ads_watched_today' => $adsWatchedToday,
                'ad_daily_limit' => $adDailyLimit,
            ]
        ]);
    }

    public function getGames(Request $request)
    {
        $query = \App\Models\Game::where('is_active', true);
        
        if ($request->has('featured') && $request->featured == 1) {
            $query->where('is_featured', true);
        }

        $games = $query->paginate(20);

        $games->getCollection()->transform(function ($game) {
            $game->image = FilePath::getUrl($game->image);
            return $game;
        });

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

        $game->image = FilePath::getUrl($game->image);

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
            'game_id' => 'nullable|string',
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
            // Check Ad Limit before processing
            if ($request->source === 'ad_watch' || $request->source === 'watch_ad') {
                $today = \Carbon\Carbon::today();
                $adsWatchedToday = AdHistory::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count();
                $adDailyLimit = (int) Setting::get('ad_daily_limit', 10);
                
                if ($adsWatchedToday >= $adDailyLimit) {
                     return response()->json([
                        'status' => false,
                        'message' => 'Daily ad limit reached'
                    ], 403);
                }
            }

            // Check Lucky Wheel Limit before processing
            if ($request->source === 'lucky_wheel' || $request->source === 'lucky_spin') {
                $today = \Carbon\Carbon::today();
                $spinsToday = LuckyWheelHistory::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count();
                $wheelDailyLimit = (int) Setting::get('lucky_wheel_limit', 10);
                
                if ($spinsToday >= $wheelDailyLimit) {
                     return response()->json([
                        'status' => false,
                        'message' => 'Daily lucky wheel limit reached'
                    ], 403);
                }
            }

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

            // Record Game History
            if ($request->source === 'game_play' || $request->source === 'game_reward') {
                 GameHistory::create([
                    'user_id' => $user->id,
                    'game_id' => $request->game_id ?? null,
                    'coins' => $request->amount,
                    'status' => 'completed',
                ]);
            }

            // Record Ad History
            if ($request->source === 'ad_watch' || $request->source === 'watch_ad') {
                AdHistory::create([
                    'user_id' => $user->id,
                    'ad_network' => 'admob', // Default or pass from request
                    'coins' => $request->amount,
                    'status' => 'completed',
                ]);
            }

            // Record Lucky Wheel History
            if ($request->source === 'lucky_wheel' || $request->source === 'lucky_spin') {
                LuckyWheelHistory::create([
                    'user_id' => $user->id,
                    'reward_amount' => $request->amount,
                ]);
            }
            
            // Calculate Daily Stats
            $today = \Carbon\Carbon::today();
            $gamesPlayedToday = GameHistory::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();
            
            $dailyLimit = (int) Setting::get('game_daily_limit', 10);
            
            $adsWatchedToday = AdHistory::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();
            $adDailyLimit = (int) Setting::get('ad_daily_limit', 10);

            $spinsToday = LuckyWheelHistory::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();
            $wheelDailyLimit = (int) Setting::get('lucky_wheel_limit', 10);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Balance updated successfully',
                'data' => [
                    'coins' => $user->coins,
                    'gems' => $user->gems,
                    'level' => $user->level,
                    'games_played_today' => $gamesPlayedToday,
                    'daily_limit' => $dailyLimit,
                    'ads_watched_today' => $adsWatchedToday,
                    'ad_daily_limit' => $adDailyLimit,
                    'lucky_wheel_spins_today' => $spinsToday,
                    'lucky_wheel_limit' => $wheelDailyLimit,
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

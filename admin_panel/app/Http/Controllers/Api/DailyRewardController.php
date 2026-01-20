<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyReward;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;

class DailyRewardController extends Controller
{
    private function getRewards()
    {
        $rewardsFromDb = DailyReward::orderBy('day')->get();
        $rewards = [];
        foreach ($rewardsFromDb as $reward) {
            $rewards[$reward->day] = [
                'coins' => $reward->coins,
                'gems' => $reward->gems
            ];
        }
        return $rewards;
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $rewards = $this->getRewards();
        
        $lastRewardAt = $user->last_daily_reward_at ? Carbon::parse($user->last_daily_reward_at) : null;
        $currentStreak = $user->daily_streak;

        $canClaim = false;
        $nextDay = 1;

        if (!$lastRewardAt) {
            $canClaim = true;
            $nextDay = 1;
        } else {
            if ($lastRewardAt->isToday()) {
                $canClaim = false;
                $nextDay = $currentStreak + 1;
                if ($nextDay > 15) $nextDay = 1;
            } elseif ($lastRewardAt->isYesterday()) {
                $canClaim = true;
                $nextDay = $currentStreak + 1;
                if ($nextDay > 15) $nextDay = 1;
            } else {
                // Missed a day
                $canClaim = true;
                $nextDay = 1;
            }
        }
        
        // Prepare reward list with status
        $rewardsList = [];
        foreach ($rewards as $day => $reward) {
            $status = 'locked'; // locked, claimable, claimed
            
            $effectiveStreak = $currentStreak;
            if ($lastRewardAt && !$lastRewardAt->isToday() && !$lastRewardAt->isYesterday()) {
                $effectiveStreak = 0;
            }
            
            // Fix for cycle reset: If streak is 15 and we can claim (meaning it's the next day), 
            // visually reset to 0 so Day 1 becomes claimable.
            if ($currentStreak >= 15 && $canClaim) {
                $effectiveStreak = 0;
            }

            if ($day <= $effectiveStreak) {
                $status = 'claimed';
            } elseif ($day == $effectiveStreak + 1) {
                if ($canClaim) {
                    $status = 'claimable';
                } else {
                    $status = 'locked'; 
                }
            } else {
                $status = 'locked';
            }
            
            // Re-evaluating UI status logic:
            if ($lastRewardAt && $lastRewardAt->isToday()) {
                // Claimed today.
                if ($day <= $currentStreak) {
                    $status = 'claimed';
                } else {
                    $status = 'locked';
                }
            } else {
                // Not claimed today.
                // If missed day, effective streak 0.
                if ($lastRewardAt && !$lastRewardAt->isYesterday() && !$lastRewardAt->isToday()) {
                     // Missed, so Day 1 is claimable.
                     if ($day == 1) $status = 'claimable';
                     else $status = 'locked';
                } else {
                    // Streak intact.
                    if ($day <= $currentStreak) $status = 'claimed';
                    elseif ($day == $currentStreak + 1) $status = 'claimable';
                    else $status = 'locked';
                }
            }
            
            $rewardsList[] = [
                'day' => $day,
                'reward' => $reward,
                'status' => $status
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'can_claim' => $canClaim,
                'current_streak' => $user->daily_streak,
                'next_day' => $nextDay,
                'rewards' => $rewardsList
            ]
        ]);
    }

    public function claim(Request $request)
    {
        $user = $request->user();
        $rewards = $this->getRewards();
        $lastRewardAt = $user->last_daily_reward_at ? Carbon::parse($user->last_daily_reward_at) : null;
        
        if ($lastRewardAt && $lastRewardAt->isToday()) {
            return response()->json([
                'status' => false,
                'message' => 'Already claimed today.'
            ], 400);
        }

        $streak = $user->daily_streak;

        // Calculate new streak
        if (!$lastRewardAt || $lastRewardAt->isYesterday()) {
            $streak++;
        } else {
            // Missed a day
            $streak = 1;
        }

        // Cycle logic
        if ($streak > 15) {
            $streak = 1;
        }

        // Get reward
        if (!isset($rewards[$streak])) {
            // Fallback or error, but shouldn't happen if DB seeded
             return response()->json([
                'status' => false,
                'message' => 'Reward configuration error.'
            ], 500);
        }
        $reward = $rewards[$streak];
        
        DB::beginTransaction();
        try {
            // Update user
            $user->daily_streak = $streak;
            $user->last_daily_reward_at = Carbon::now();
            $user->coins += $reward['coins'];
            $user->gems += $reward['gems'];
            $user->save();

            // Record Transaction for Coins
            if ($reward['coins'] > 0) {
                TransactionHistory::create([
                    'user_id' => $user->id,
                    'type' => 'coin',
                    'amount' => $reward['coins'],
                    'source' => 'daily_reward',
                    'description' => 'Day ' . $streak . ' Daily Reward',
                ]);
            }

            // Record Transaction for Gems
            if ($reward['gems'] > 0) {
                TransactionHistory::create([
                    'user_id' => $user->id,
                    'type' => 'gem',
                    'amount' => $reward['gems'],
                    'source' => 'daily_reward',
                    'description' => 'Day ' . $streak . ' Daily Reward',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Reward claimed successfully!',
                'data' => [
                    'reward' => $reward,
                    'new_balance' => [
                        'coins' => $user->coins,
                        'gems' => $user->gems
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to claim reward: ' . $e->getMessage()
            ], 500);
        }
    }
}

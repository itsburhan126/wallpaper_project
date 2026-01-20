<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        // Get top 50 users by coins
        $users = User::select('id', 'name', 'avatar', 'coins', 'level')
            ->orderBy('coins', 'desc')
            ->limit(50)
            ->get();

        // Optional: Find current user's rank
        $userRank = null;
        if ($request->user()) {
            $rank = User::where('coins', '>', $request->user()->coins)->count() + 1;
            $userRank = [
                'rank' => $rank,
                'user' => $request->user()->only(['id', 'name', 'avatar', 'coins', 'level']),
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $users,
            'user_rank' => $userRank,
        ]);
    }
}

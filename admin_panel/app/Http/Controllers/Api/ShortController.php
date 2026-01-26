<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Short;
use App\Models\ShortComment;
use App\Models\TransactionHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShortController extends Controller
{
    public function index()
    {
        $userId = auth('sanctum')->id();

        $query = Short::where('is_active', true)
            ->withCount(['likes', 'comments'])
            ->latest();

        if ($userId) {
            $query->withExists(['likes as is_liked' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);
        }

        // Use simplePaginate for better performance on large datasets
        $shorts = $query->simplePaginate(15);

        $shorts->through(function ($short) {
            // Ensure boolean type for is_liked
            $short->is_liked = isset($short->is_liked) ? (bool) $short->is_liked : false;

            // Ensure full URL is returned for API consumers
            if ($short->video_url && !str_starts_with($short->video_url, 'http')) {
                $short->video_url = asset($short->video_url);
            }
            if ($short->thumbnail_url && !str_starts_with($short->thumbnail_url, 'http')) {
                $short->thumbnail_url = asset($short->thumbnail_url);
            }
            return $short;
        });
            
        return response()->json([
            'success' => true,
            'data' => $shorts->items(),
            'pagination' => [
                'current_page' => $shorts->currentPage(),
                'next_page_url' => $shorts->nextPageUrl(),
                'prev_page_url' => $shorts->previousPageUrl(),
                'has_more' => $shorts->hasMorePages(),
                'per_page' => $shorts->perPage(),
            ]
        ]);
    }

    public function toggleLike($id)
    {
        $short = Short::find($id);
        if (!$short) {
            return response()->json(['success' => false, 'message' => 'Short not found'], 404);
        }

        $user = auth()->user();
        $like = $short->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $short->likes()->detach($user->id);
            $liked = false;
        } else {
            $short->likes()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'is_liked' => $liked,
            'likes_count' => $short->likes()->count()
        ]);
    }

    public function getComments($id)
    {
        $short = Short::find($id);
        if (!$short) {
            return response()->json(['success' => false, 'message' => 'Short not found'], 404);
        }

        $comments = $short->comments()
            ->with('user:id,name,avatar')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:500',
        ]);

        $short = Short::find($id);
        if (!$short) {
            return response()->json(['success' => false, 'message' => 'Short not found'], 404);
        }

        $comment = $short->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment->load('user:id,name,avatar')
        ]);
    }

    public function reward(Request $request, $id)
    {
        $short = Short::find($id);
        if (!$short) {
            return response()->json(['success' => false, 'message' => 'Short not found'], 404);
        }

        $user = auth()->user();
        $rewardAmount = 10; // Default reward

        // Fetch from settings if available
        $setting = Setting::where('key', 'shorts_reward')->first();
        if ($setting) {
            $rewardAmount = (int) $setting->value;
        }

        try {
            DB::beginTransaction();
            
            $user->coins += $rewardAmount;
            $user->save();
            
            TransactionHistory::create([
                'user_id' => $user->id,
                'type' => 'coin',
                'amount' => $rewardAmount,
                'source' => 'shorts_ad',
                'description' => 'Reward for watching short ad',
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Reward claimed successfully',
                'coins_added' => $rewardAmount,
                'new_balance' => $user->coins
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to claim reward: ' . $e->getMessage()], 500);
        }
    }

    public function incrementView($id)
    {
        $short = Short::find($id);
        if (!$short) {
            return response()->json(['success' => false, 'message' => 'Short not found'], 404);
        }

        $short->increment('views');

        return response()->json([
            'success' => true,
            'message' => 'View counted',
            'views' => $short->views
        ]);
    }
}
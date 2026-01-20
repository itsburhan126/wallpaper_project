<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(20);
        
        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }
    
    public function markAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}

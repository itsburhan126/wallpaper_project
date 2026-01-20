<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        $totalRedeemRequests = RedeemRequest::count();
        $todayRedeemRequests = RedeemRequest::whereDate('created_at', Carbon::today())->count();

        $topUsers = User::orderBy('coins', 'desc')->take(10)->get();
        $recentRedeemRequests = RedeemRequest::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'todayUsers',
            'totalRedeemRequests',
            'todayRedeemRequests',
            'topUsers',
            'recentRedeemRequests'
        ));
    }
}

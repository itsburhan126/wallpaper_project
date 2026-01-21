<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RedeemRequest;
use App\Models\ReferralHistory;
use App\Models\TransactionHistory;
use App\Models\AdWatchLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount(['referrals', 'redeemRequests'])->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(Request $request, $id)
    {
        $user = User::withCount(['referrals'])->findOrFail($id);
        
        // Check if a specific redeem request is being viewed
        $currentRequest = null;
        if ($request->has('request_id')) {
            $currentRequest = RedeemRequest::with('method.gateway')->find($request->request_id);
        }

        // Fetch related data
        $redeemRequests = RedeemRequest::where('user_id', $id)->with('method.gateway')->latest()->limit(10)->get();
        $transactions = TransactionHistory::where('user_id', $id)->latest()->limit(20)->get();
        $adWatches = AdWatchLog::where('user_id', $id)->latest()->limit(20)->get();
        $referrals = ReferralHistory::where('referrer_id', $id)->with('referredUser')->latest()->limit(20)->get();

        return view('admin.users.show', compact('user', 'redeemRequests', 'transactions', 'adWatches', 'referrals', 'currentRequest'));
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status; // Toggle boolean
        $user->save();
        
        // If blocked (status is false), maybe revoke tokens?
        if (!$user->status) {
            $user->tokens()->delete();
        }

        return back()->with('success', 'User status updated to ' . ($user->status ? 'Active' : 'Blocked'));
    }

    public function transactions($id)
    {
        $user = User::findOrFail($id);
        $transactions = TransactionHistory::where('user_id', $id)->latest()->paginate(20);
        return view('admin.users.transactions', compact('user', 'transactions'));
    }

    public function redeems($id)
    {
        $user = User::findOrFail($id);
        $redeems = RedeemRequest::where('user_id', $id)->with('method.gateway')->latest()->paginate(20);
        return view('admin.users.redeems', compact('user', 'redeems'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete related data if necessary or rely on cascade
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}

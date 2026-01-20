<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckDemoMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in as admin and has demo-admin role
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('demo-admin')) {
            
            // Allow GET requests and Logout
            if ($request->isMethod('get') || Route::is('admin.logout')) {
                return $next($request);
            }

            // Block everything else
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Action disabled in Demo Mode.'], 403);
            }

            return back()->with('error', 'This action is disabled in Demo Mode.');
        }

        return $next($request);
    }
}

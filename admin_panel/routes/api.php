<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\DailyRewardController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\RedeemController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WallpaperController;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);
Route::post('/check-user', [AuthController::class, 'checkUser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/add-referrer', [AuthController::class, 'addReferrer']);
    
    // Daily Rewards
    Route::get('/daily-rewards', [DailyRewardController::class, 'status']);
    Route::post('/daily-rewards/claim', [DailyRewardController::class, 'claim']);

    // Game Balance Updates
    Route::post('/game/update-balance', [GameController::class, 'updateBalance']);
    Route::post('/game/log-ad', [GameController::class, 'logAdWatch']);
    Route::post('/game/increment-play-count', [GameController::class, 'incrementPlayCount']);
    Route::get('/game/status', [GameController::class, 'getStatus']);

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    // Redeem
    Route::get('/redeem/gateways', [RedeemController::class, 'gateways']);
    Route::get('/redeem/methods/{gatewayId}', [RedeemController::class, 'methods']);
    Route::post('/redeem/request', [RedeemController::class, 'store']);
    Route::get('/redeem/history', [RedeemController::class, 'history']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Shorts Interactions
    Route::post('/shorts/{id}/like', [\App\Http\Controllers\Api\ShortController::class, 'toggleLike']);
    Route::post('/shorts/{id}/comments', [\App\Http\Controllers\Api\ShortController::class, 'storeComment']);
    Route::post('/shorts/{id}/reward', [\App\Http\Controllers\Api\ShortController::class, 'reward']);

    // Support
    Route::get('/support/tickets', [\App\Http\Controllers\Api\SupportController::class, 'index']);
    Route::post('/support/tickets', [\App\Http\Controllers\Api\SupportController::class, 'store']);
    Route::get('/support/tickets/{id}', [\App\Http\Controllers\Api\SupportController::class, 'show']);
    Route::post('/support/tickets/{id}/reply', [\App\Http\Controllers\Api\SupportController::class, 'reply']);
});

// Public APIs for App
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/categories', [WallpaperController::class, 'getCategories']);
Route::get('/games', [GameController::class, 'getGames']);
Route::get('/games/{id}', [GameController::class, 'getGame']);

// Wallpapers
Route::get('/wallpapers', [WallpaperController::class, 'index']);
Route::get('/wallpapers/{id}', [WallpaperController::class, 'show']);
Route::post('/wallpapers/{id}/download', [WallpaperController::class, 'download']);

// Settings
Route::get('/settings/ads', [SettingController::class, 'index']);
Route::get('/settings/general', [SettingController::class, 'general']);
Route::get('/settings/game', [SettingController::class, 'game']);
Route::get('/settings/api', [SettingController::class, 'api']);
Route::get('/settings/offerwall', [SettingController::class, 'offerwall']);
Route::get('/settings/shorts', [SettingController::class, 'shorts']);
Route::get('/settings/security', [SettingController::class, 'security']);

// Shorts
Route::get('/shorts', [\App\Http\Controllers\Api\ShortController::class, 'index']);
Route::get('/shorts/{id}/comments', [\App\Http\Controllers\Api\ShortController::class, 'getComments']);

// Pages
Route::get('/pages/{slug}', [PageController::class, 'show']);

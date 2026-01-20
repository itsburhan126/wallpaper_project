<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DailyRewardController;
use App\Http\Controllers\Admin\SpinRewardController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return view('auth.verification-success');
})->middleware(['signed'])->name('verification.verify');

// Password Reset Routes
Route::get('password/reset/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');
Route::get('password/success', [\App\Http\Controllers\PasswordResetController::class, 'success'])->name('password.success');

Route::get('login', function() {
    return redirect()->route('admin.login');
})->name('login');

// Public Page Route
Route::get('/page/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin Routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware(['auth:admin', 'admin.demo'])->group(function () {
        
        // Categories & Games
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('games', \App\Http\Controllers\Admin\GameController::class);
        Route::resource('wallpapers', \App\Http\Controllers\Admin\WallpaperController::class);
        Route::resource('shorts', \App\Http\Controllers\Admin\ShortController::class);

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // Profile
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        Route::middleware('admin.permission:dashboard_access')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        });
        
        // Banners
        Route::middleware('admin.permission:manage_banners')->group(function () {
            Route::post('banners/bulk-destroy', [BannerController::class, 'bulkDestroy'])->name('banners.bulk_destroy');
            Route::resource('banners', BannerController::class);
        });

        // Settings
        Route::middleware('admin.permission:manage_settings')->group(function () {
            // Daily Rewards
            Route::get('daily-rewards', [DailyRewardController::class, 'index'])->name('daily_rewards.index');
            Route::post('daily-rewards', [DailyRewardController::class, 'store'])->name('daily_rewards.store');

            Route::get('settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('settings.general');
            Route::post('settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'updateGeneral'])->name('settings.general.update');
    
            Route::get('settings/ads', [\App\Http\Controllers\Admin\SettingController::class, 'ads'])->name('settings.ads');
            Route::post('settings/ads', [\App\Http\Controllers\Admin\SettingController::class, 'updateAds'])->name('settings.ads.update');

            Route::get('settings/promotion', [\App\Http\Controllers\Admin\SettingController::class, 'promotion'])->name('settings.promotion');
            Route::post('settings/promotion', [\App\Http\Controllers\Admin\SettingController::class, 'updatePromotion'])->name('settings.promotion.update');
    
            Route::get('settings/api', [\App\Http\Controllers\Admin\SettingController::class, 'api'])->name('settings.api');
            Route::post('settings/api', [\App\Http\Controllers\Admin\SettingController::class, 'updateApi'])->name('settings.api.update');

            // Route::get('settings/offerwall', [\App\Http\Controllers\Admin\SettingController::class, 'offerwall'])->name('settings.offerwall');
            // Route::post('settings/offerwall', [\App\Http\Controllers\Admin\SettingController::class, 'updateOfferwall'])->name('settings.offerwall.update');

            Route::get('settings/email', [\App\Http\Controllers\Admin\SettingController::class, 'email'])->name('settings.email');
            Route::post('settings/email', [\App\Http\Controllers\Admin\SettingController::class, 'updateEmail'])->name('settings.email.update');

            Route::get('settings/referral', [\App\Http\Controllers\Admin\SettingController::class, 'referral'])->name('settings.referral');
            Route::post('settings/referral', [\App\Http\Controllers\Admin\SettingController::class, 'updateReferral'])->name('settings.referral.update');

            Route::get('settings/game', [\App\Http\Controllers\Admin\SettingController::class, 'game'])->name('settings.game');
            Route::post('settings/game', [\App\Http\Controllers\Admin\SettingController::class, 'updateGame'])->name('settings.game.update');

            Route::get('settings/shorts', [\App\Http\Controllers\Admin\SettingController::class, 'shorts'])->name('settings.shorts');
            Route::post('settings/shorts', [\App\Http\Controllers\Admin\SettingController::class, 'updateShorts'])->name('settings.shorts.update');

            Route::get('settings/deep-link', [\App\Http\Controllers\Admin\SettingController::class, 'deepLink'])->name('settings.deep_link');
            Route::post('settings/deep-link', [\App\Http\Controllers\Admin\SettingController::class, 'updateDeepLink'])->name('settings.deep_link.update');

            // Redeem Requests
            Route::get('redeem/requests', [\App\Http\Controllers\Admin\RedeemController::class, 'requests'])->name('redeem.requests.index');
            Route::put('redeem/requests/{id}', [\App\Http\Controllers\Admin\RedeemController::class, 'updateRequestStatus'])->name('redeem.requests.update');

            // Redeem Gateways
            Route::post('redeem/gateways/bulk-destroy', [\App\Http\Controllers\Admin\RedeemController::class, 'bulkDestroy'])->name('redeem.gateways.bulk_destroy');
            Route::get('redeem/gateways', [\App\Http\Controllers\Admin\RedeemController::class, 'index'])->name('redeem.gateways.index');
            Route::post('redeem/gateways', [\App\Http\Controllers\Admin\RedeemController::class, 'store'])->name('redeem.gateways.store');
            Route::put('redeem/gateways/{id}', [\App\Http\Controllers\Admin\RedeemController::class, 'update'])->name('redeem.gateways.update');
            Route::delete('redeem/gateways/{id}', [\App\Http\Controllers\Admin\RedeemController::class, 'destroy'])->name('redeem.gateways.destroy');

            // Redeem Methods
            Route::get('redeem/gateways/{gateway}/methods', [\App\Http\Controllers\Admin\RedeemController::class, 'methods'])->name('redeem.methods.index');
            Route::post('redeem/gateways/{gateway}/methods', [\App\Http\Controllers\Admin\RedeemController::class, 'storeMethod'])->name('redeem.methods.store');
            Route::put('redeem/methods/{id}', [\App\Http\Controllers\Admin\RedeemController::class, 'updateMethod'])->name('redeem.methods.update');
            Route::delete('redeem/methods/{id}', [\App\Http\Controllers\Admin\RedeemController::class, 'destroyMethod'])->name('redeem.methods.destroy');
        });

        // Pages
        Route::middleware('admin.permission:manage_pages')->group(function () {
            Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->only(['index', 'edit', 'update']);
        });

        // Staff & Roles
        Route::middleware('admin.permission:manage_staff')->group(function () {
            Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
            Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
        });

        // User Management
        Route::middleware('admin.permission:manage_users')->group(function () {
            Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
            Route::get('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
            Route::delete('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
            Route::put('users/{id}/block', [\App\Http\Controllers\Admin\UserController::class, 'toggleBlock'])->name('users.block');
            Route::get('users/{id}/transactions', [\App\Http\Controllers\Admin\UserController::class, 'transactions'])->name('users.transactions');
            Route::get('users/{id}/redeems', [\App\Http\Controllers\Admin\UserController::class, 'redeems'])->name('users.redeems');
        });
    });
});

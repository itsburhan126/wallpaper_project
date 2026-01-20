<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users Table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->string('google_id')->nullable();
                $table->string('avatar')->nullable();
                $table->boolean('status')->default(true);
                $table->bigInteger('coins')->default(0);
                $table->bigInteger('gems')->default(0);
                $table->integer('level')->default(1);
                $table->string('referral_code')->unique()->nullable();
                $table->string('referred_by')->nullable();
                $table->integer('daily_streak')->default(0);
                $table->timestamp('last_daily_reward_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Redeem Gateways
        if (!Schema::hasTable('redeem_gateways')) {
            Schema::create('redeem_gateways', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->nullable();
                $table->integer('priority')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Redeem Methods
        if (!Schema::hasTable('redeem_methods')) {
            Schema::create('redeem_methods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('redeem_gateway_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->integer('coin_cost');
                $table->decimal('amount', 10, 2);
                $table->string('currency');
                $table->string('input_hint')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Redeem Requests
        if (!Schema::hasTable('redeem_requests')) {
            Schema::create('redeem_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('method_id')->nullable()->constrained('redeem_methods')->onDelete('set null');
                $table->string('gateway_name');
                $table->integer('coin_cost');
                $table->decimal('amount', 10, 2);
                $table->string('currency');
                $table->text('account_details');
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }

        // Transaction Histories
        if (!Schema::hasTable('transaction_histories')) {
            Schema::create('transaction_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // credit, debit
                $table->decimal('amount', 15, 2); // coins or cash value? Model says amount. Usually coins.
                $table->string('source'); // game, redeem, referral, bonus
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Ad Watch Logs
        if (!Schema::hasTable('ad_watch_logs')) {
            Schema::create('ad_watch_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('provider'); // admob, applovin, unity
                $table->string('ad_type'); // interstitial, rewarded
                $table->string('status'); // completed, skipped, failed
                $table->string('reward_type')->nullable(); // coins
                $table->integer('reward_amount')->default(0);
                $table->timestamps();
            });
        }

        // Daily Rewards
        if (!Schema::hasTable('daily_rewards')) {
            Schema::create('daily_rewards', function (Blueprint $table) {
                $table->id();
                $table->integer('day'); // 1 to 7
                $table->integer('coins')->default(0);
                $table->integer('gems')->default(0);
                $table->timestamps();
            });
        }

        // Referral Histories
        if (!Schema::hasTable('referral_histories')) {
            Schema::create('referral_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('referred_user_id')->constrained('users')->onDelete('cascade');
                $table->integer('level')->default(1);
                $table->integer('bonus_amount')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_histories');
        Schema::dropIfExists('daily_rewards');
        Schema::dropIfExists('ad_watch_logs');
        Schema::dropIfExists('transaction_histories');
        Schema::dropIfExists('redeem_requests');
        Schema::dropIfExists('redeem_methods');
        Schema::dropIfExists('redeem_gateways');
        Schema::dropIfExists('users');
    }
};

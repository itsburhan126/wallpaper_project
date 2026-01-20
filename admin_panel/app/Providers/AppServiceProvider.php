<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            // Global View Shares
            $currency_symbol = Setting::get('currency_symbol', '$');
            View::share('currency_symbol', $currency_symbol);

            // Mail Configuration Override
            $senderEmail = Setting::get('sender_email') ?? Setting::get('mail_from_address');
            if ($senderEmail) {
                config(['mail.from.address' => $senderEmail]);
            }
            
            $senderName = Setting::get('mail_from_name');
            if ($senderName) {
                config(['mail.from.name' => $senderName]);
            }

            // SMTP Configuration
            $mailer = Setting::get('mail_mailer');
            if ($mailer && $mailer === 'smtp') {
                config(['mail.default' => 'smtp']);
                
                $host = Setting::get('mail_host');
                if ($host) config(['mail.mailers.smtp.host' => $host]);

                $port = Setting::get('mail_port');
                if ($port) config(['mail.mailers.smtp.port' => $port]);

                $username = Setting::get('mail_username');
                if ($username) config(['mail.mailers.smtp.username' => $username]);

                $password = Setting::get('mail_password');
                if ($password) config(['mail.mailers.smtp.password' => $password]);

                $encryption = Setting::get('mail_encryption');
                if ($encryption) config(['mail.mailers.smtp.encryption' => $encryption]);
            }

            $appName = Setting::get('app_name');
            if ($appName) {
                config(['app.name' => $appName]);
                // If mail_from_name is not set specifically, use app_name
                if (!$senderName) {
                    config(['mail.from.name' => $appName]);
                }
            }
        }
    }
}

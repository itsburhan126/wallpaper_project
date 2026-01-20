<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class UpdateFirebaseConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:firebase-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Firebase configuration in settings table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = [
            'firebase_api_key' => 'AIzaSyBNxnQgbSZBd3uCgQXSxHavAdy7mqirZ88',
            'firebase_auth_domain' => 'earning-app-c9950.firebaseapp.com',
            'firebase_project_id' => 'earning-app-c9950',
            'firebase_storage_bucket' => 'earning-app-c9950.appspot.com',
            'firebase_messaging_sender_id' => '1033704485909',
            'firebase_app_id' => '1:1033704485909:android:fed00e922170d63a0e0a1c',
            'firebase_measurement_id' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            $this->info("Updated $key");
        }
        
        $this->info("Firebase configuration updated successfully.");
    }
}

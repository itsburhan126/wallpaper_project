<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RedeemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            ['name' => 'PayPal', 'image' => 'gateways/paypal.png', 'hint' => 'Enter your PayPal email'],
            ['name' => 'Bkash', 'image' => 'gateways/bkash.png', 'hint' => 'Enter your Bkash number'],
            ['name' => 'Nagad', 'image' => 'gateways/nagad.png', 'hint' => 'Enter your Nagad number'],
            ['name' => 'Rocket', 'image' => 'gateways/rocket.png', 'hint' => 'Enter your Rocket number'],
            ['name' => 'Paytm', 'image' => 'gateways/paytm.png', 'hint' => 'Enter your Paytm number'],
            ['name' => 'Google Play', 'image' => 'gateways/google_play.png', 'hint' => 'Enter your Email'],
            ['name' => 'Amazon Gift Card', 'image' => 'gateways/amazon.png', 'hint' => 'Enter your Email'],
            ['name' => 'Steam Wallet', 'image' => 'gateways/steam.png', 'hint' => 'Enter your Steam ID'],
            ['name' => 'Bitcoin', 'image' => 'gateways/bitcoin.png', 'hint' => 'Enter your Wallet Address'],
            ['name' => 'USDT', 'image' => 'gateways/usdt.png', 'hint' => 'Enter your TRC20 Address'],
        ];

        foreach ($gateways as $gateway) {
            
            $existing = DB::table('redeem_gateways')->where('name', $gateway['name'])->first();
            
            if ($existing) {
                $gatewayId = $existing->id;
                DB::table('redeem_gateways')->where('id', $gatewayId)->update([
                    'icon' => $gateway['image'], // renamed image -> icon
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                $gatewayId = DB::table('redeem_gateways')->insertGetId([
                    'name' => $gateway['name'],
                    'icon' => $gateway['image'], // renamed image -> icon
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Add 1-2 methods for each gateway
            $methods = [
                [
                    'name' => '$5 ' . $gateway['name'],
                    'coin_cost' => 5000,
                    'amount' => 5.00,
                    'currency' => 'USD',
                ],
                [
                    'name' => '$10 ' . $gateway['name'],
                    'coin_cost' => 10000,
                    'amount' => 10.00,
                    'currency' => 'USD',
                ]
            ];

            foreach ($methods as $method) {
                DB::table('redeem_methods')->updateOrInsert(
                    ['redeem_gateway_id' => $gatewayId, 'name' => $method['name']], // renamed gateway_id -> redeem_gateway_id
                    [
                        'coin_cost' => $method['coin_cost'],
                        'amount' => $method['amount'],
                        'currency' => $method['currency'],
                        'input_hint' => $gateway['hint'],
                        'is_active' => true,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

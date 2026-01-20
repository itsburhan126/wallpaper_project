<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing pages to avoid duplicates
        Page::truncate();

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<div class="space-y-6">
    <p class="text-lg text-gray-600">Welcome to <strong>Bubble Shooter Pro</strong>, the ultimate destination for puzzle enthusiasts and competitive gamers alike. Our mission is simple: to deliver the most engaging, visually stunning, and rewarding bubble shooting experience on mobile.</p>
    
    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Who We Are</h3>
    <p class="text-gray-600">We are a passionate team of developers, designers, and gamers dedicated to creating high-quality entertainment. With Bubble Shooter Pro, we have combined classic arcade mechanics with modern competitive features, allowing players to not only relax but also challenge friends and earn rewards.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Our Vision</h3>
    <p class="text-gray-600">We believe in fair play and fun. Our platform is built to ensure a seamless experience where skill is the only factor that matters. Whether you are here to kill time or climb the leaderboards, we are here to support your journey.</p>
    
    <p class="text-gray-600 mt-4">Thank you for being part of our community. Happy Popping!</p>
</div>',
                'is_active' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<div class="space-y-6">
    <p class="text-gray-600">Last updated: January 2026</p>
    <p class="text-gray-600">At Bubble Shooter Pro, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our mobile application.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">1. Information We Collect</h3>
    <ul class="list-disc list-inside text-gray-600 space-y-2">
        <li><strong>Personal Data:</strong> Name, email address, and profile image when you register or sign in with Google.</li>
        <li><strong>Usage Data:</strong> Information about your game progress, scores, and interactions within the app.</li>
        <li><strong>Device Data:</strong> IP address, device type, and operating system version for security and optimization.</li>
    </ul>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">2. How We Use Your Information</h3>
    <p class="text-gray-600">We use the information we collect to:</p>
    <ul class="list-disc list-inside text-gray-600 space-y-2">
        <li>Create and manage your account.</li>
        <li>Process your in-game rewards and referral bonuses.</li>
        <li>Prevent fraudulent activity and ensure fair play.</li>
        <li>Improve game performance and user experience.</li>
    </ul>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">3. Data Security</h3>
    <p class="text-gray-600">We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable.</p>
</div>',
                'is_active' => true,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'content' => '<div class="space-y-6">
    <p class="text-gray-600">Please read these Terms and Conditions carefully before using the Bubble Shooter Pro mobile application.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">1. Acceptance of Terms</h3>
    <p class="text-gray-600">By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">2. User Accounts</h3>
    <p class="text-gray-600">When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">3. Virtual Currency (Gems & Coins)</h3>
    <p class="text-gray-600">The Service may include virtual currency, such as "Gems" or "Coins". These virtual items have no real-world monetary value and cannot be exchanged for real money. They are for entertainment purposes within the game only.</p>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">4. Prohibited Conduct</h3>
    <p class="text-gray-600">You agree not to use the Service to:</p>
    <ul class="list-disc list-inside text-gray-600 space-y-2">
        <li>Engage in any form of cheating, hacking, or botting.</li>
        <li>Harass, abuse, or harm another person or group.</li>
        <li>Interfere with or disrupt the Service or servers.</li>
    </ul>

    <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">5. Termination</h3>
    <p class="text-gray-600">We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
</div>',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}

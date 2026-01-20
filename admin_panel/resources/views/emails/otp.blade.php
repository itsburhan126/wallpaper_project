<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #00B251; text-align: center;">{{ config('app.name') }}</h2>
        <h3 style="color: #333; text-align: center;">Verification Code</h3>
        <p style="color: #666; font-size: 16px; line-height: 1.5; text-align: center;">
            Use the following One-Time Password (OTP) to complete your verification. This code is valid for 10 minutes.
        </p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333; background-color: #f0fdf4; padding: 15px 30px; border-radius: 8px; border: 2px dashed #00B251;">
                {{ $otp }}
            </span>
        </div>
        <p style="color: #666; font-size: 14px; text-align: center;">
            If you did not request this code, please ignore this email.
        </p>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 12px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

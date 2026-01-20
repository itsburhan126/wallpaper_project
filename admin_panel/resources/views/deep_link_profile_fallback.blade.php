<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile in {{ \App\Models\Setting::get('deep_link_app_name', 'My Game') }}</title>
    <style>
        :root {
            --primary-color: #6200ea; /* Deep Purple to match app theme */
            --primary-hover: #3700b3;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-main: #202124;
            --text-secondary: #5f6368;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: var(--text-main);
        }

        .container {
            background: var(--card-bg);
            width: 100%;
            max-width: 400px;
            padding: 40px 24px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            text-align: center;
            margin: 20px;
            box-sizing: border-box;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .app-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6200ea, #9d46ff);
            border-radius: 50%;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(98, 0, 234, 0.3);
        }

        .app-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
            color: var(--text-main);
        }

        p {
            font-size: 15px;
            color: var(--text-secondary);
            margin: 0 0 32px;
            line-height: 1.5;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 16px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, background-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(98, 0, 234, 0.2);
            border: none;
            cursor: pointer;
            box-sizing: border-box; 
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(98, 0, 234, 0.3);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            margin-top: 12px;
            box-shadow: none;
        }

        .btn-outline:hover {
            background-color: rgba(98, 0, 234, 0.05);
            box-shadow: none;
            transform: translateY(-2px);
        }

        .spinner-container {
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(98, 0, 234, 0.1);
            border-left-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #9aa0a6;
        }
        
        /* Profile Preview Placeholder */
        .profile-preview {
            background: #f1f3f4;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }
        .preview-icon {
            width: 40px;
            height: 40px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #757575;
        }
        .preview-text {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

    </style>
</head>
<body>

    <div class="container">
        <!-- App Icon -->
        <div class="app-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>

        <h1>{{ \App\Models\Setting::get('deep_link_app_name', 'My Game') }}</h1>
        <p>Check out this profile on {{ \App\Models\Setting::get('deep_link_app_name', 'My Game') }}.<br>Opening app...</p>

        <!-- Profile Preview (Simulated) -->
        <div class="profile-preview">
            <div class="preview-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <div class="preview-text">
                Viewing User #{{ $userId }}
            </div>
        </div>

        @php
            $scheme = \App\Models\Setting::get('deep_link_app_scheme', 'buysellpro://');
            $deepLink = $scheme . 'user/' . $userId;
            $storeUrl = \App\Models\Setting::get('deep_link_fallback_url', '#');
        @endphp

        <a id="openAppBtn" href="{{ $deepLink }}" class="btn">Open Profile</a>
        
        @if($storeUrl != '#')
        <a href="{{ $storeUrl }}" class="btn btn-outline">Download App</a>
        @endif

        <div class="spinner-container">
            <div class="spinner"></div>
            <span>Redirecting automatically...</span>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('deep_link_app_name', 'My Game') }}. All rights reserved.
        </div>
    </div>

    <script>
        var deepLink = "{{ $deepLink }}";
        var storeUrl = "{{ $storeUrl }}";
        
        // Try to redirect immediately
        window.location.href = deepLink;
        
        // Retry logic
        setTimeout(function() {
            window.location.href = deepLink;
            
            if (storeUrl && storeUrl !== '#') {
                setTimeout(function() {
                     if (!document.hidden) {
                        // Optional auto redirect
                     }
                }, 2000);
            }
        }, 1000);

        // Manual button handler
        document.getElementById('openAppBtn').onclick = function() {
            window.location.href = deepLink;
        };

        // Detect if app opened (page becomes hidden)
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                // App opened successfully
                console.log("App opened");
            }
        });
    </script>

</body>
</html>
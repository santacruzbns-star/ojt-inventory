<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Goldtown | Inventory Tracking</title>
    <link rel="icon" type="image/x-icon" href="storage/img/goldtown2.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0d324d;
            --accent-blue: #1e6091;
            --text-main: #ffffff;
            --border-light: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', 'Poppins', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            overflow: hidden;

            /* Modern Box Grid Background */
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: center center;
            background-attachment: fixed;
        }

        /* ============================================
               Layout Wrapper
           ============================================ */
        .login-form {
            position: relative;
            width: 100%;
            max-width: 420px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- Modern White & Blue Card Theme --- */
        .login-card {
            position: relative; /* Added for absolute positioning of share button */
            width: 100%;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            padding: 48px 36px;
            box-shadow: 
                0 20px 40px -10px rgba(0, 0, 0, 0.25),
                0 10px 20px -5px rgba(0, 0, 0, 0.15),
                inset 0 1px 1px rgba(255, 255, 255, 1);
            text-align: center;
            animation: slideUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        /* --- Share Button --- */
        .share-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(30, 96, 145, 0.1);
            border: 1px solid rgba(30, 96, 145, 0.2);
            color: var(--accent-blue);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 20;
        }

        .share-btn:hover {
            background: var(--accent-blue);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 96, 145, 0.3);
        }

        .share-btn:active {
            transform: translateY(0);
        }

        .logo {
            width: 160px;
            height: auto;
            object-fit: contain;
            margin-bottom: 24px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: translateY(-3px);
        }

        /* ============================================
               Modern Form Elements (For your Slot)
           ============================================ */
        .form-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .form-header h3 {
            color: var(--bg-base);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-header p {
            color: #64748b;
            font-size: 13px;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
            gap: 18px;
            text-align: left;
            width: 100%;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .login-card label {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent-blue);
            letter-spacing: 0.5px;
        }

        /* Modern Input Wrapper with Icons */
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.icon-left {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 16px;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .login-card input.form-control, 
        .login-card input[type="email"], 
        .login-card input[type="password"],
        .login-card input[type="text"] {
            width: 100%;
            padding: 12px 14px 12px 42px; /* Space for the icon */
            font-family: inherit;
            font-size: 14px;
            color: var(--bg-base);
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .login-card input::placeholder {
            color: #94a3b8;
        }

        /* Focus state + Icon color change */
        .login-card input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(30, 96, 145, 0.15);
        }

        .input-wrapper:focus-within i.icon-left {
            color: var(--accent-blue);
        }

        /* Checkbox & Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent-blue);
            cursor: pointer;
            margin: 0;
            padding: 0;
        }

        .checkbox-wrapper span {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        /* Buttons */
        .login-form button[type="submit"], .primary-btn {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: var(--accent-blue);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 15px rgba(30, 96, 145, 0.3);
        }

        .login-form button[type="submit"]:hover, .primary-btn:hover {
            transform: translateY(-2px);
            background: #164a72;
            box-shadow: 0 6px 20px rgba(30, 96, 145, 0.5);
        }

        .login-form button[type="submit"]:active, .primary-btn:active {
            transform: translateY(0);
        }

        /* Links */
        .login-card a {
            color: var(--accent-blue);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .login-card a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .input-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
                border-radius: 16px;
            }
        }

        /* --- Global Loader --- */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: var(--bg-base);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: transform 0.6s cubic-bezier(0.86, 0, 0.07, 1);
        }

        #global-loader.slide-up {
            transform: translateY(-100%);
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .loader-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-image {
            width: 250px;
            height: 250px;
            object-fit: contain;
            animation: imagePulse 2s infinite ease-in-out;
        }

        .loading-text {
            font-family: 'Instrument Sans', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 2px;
            animation: textPulse 2s infinite ease-in-out;
            margin: 0;
        }

        /* --- Toast Notification --- */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: var(--bg-base);
            color: #fff;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        @keyframes imagePulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        @keyframes textPulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
</head>

<body>
    <div id="toast" class="toast-notification">
        <i class="fa-solid fa-check-circle" style="color: #4ade80;"></i>
        <span>Link copied to clipboard!</span>
    </div>

    <div id="global-loader">
        <div class="loader-content">
            <div class="loader-wrapper">
                <img src="storage/img/dawg.gif" alt="Loading" class="loader-image">
            </div>
            <p class="loading-text">LOADING...</p>
        </div>
    </div>

    <div class="login-form">
        <div class="login-card">
            
            <button class="share-btn" id="shareBtn" aria-label="Share this page">
                <i class="fa-solid fa-share-nodes"></i>
            </button>

            <img class="logo" src="storage/img/login-logo.png" alt="login logo">

            {{ $slot }}

        </div>
    </div>

    <script>
        // Loader Logic
        window.addEventListener('load', () => {
            const loader = document.getElementById('global-loader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('slide-up');
                }, 400);
            }
        });

        // Share Button Logic
        document.getElementById('shareBtn').addEventListener('click', async () => {
            const shareData = {
                title: document.title,
                text: 'Check out this page on Goldtown!',
                url: window.location.href
            };

            try {
                // Try to use the native Web Share API (Works great on Mobile & modern Safari/Edge)
                if (navigator.share && navigator.canShare(shareData)) {
                    await navigator.share(shareData);
                } else {
                    // Fallback: Copy to Clipboard
                    await navigator.clipboard.writeText(shareData.url);
                    showToast();
                }
            } catch (err) {
                console.error('Error sharing:', err);
            }
        });

        // Function to show the copy success toast
        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>

</html>
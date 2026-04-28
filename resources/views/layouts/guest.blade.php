<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Goldtown | Inventory Tracking</title>
    <link rel="icon" type="image/x-icon" href="/storage/img/goldtown2.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
            /* 👇 FIXED: Allow vertical scrolling on mobile */
            overflow-x: hidden;
            overflow-y: auto;

            /* Modern Box Grid Background */
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: center center;
            background-attachment: fixed;
        }

        /* ============================================
               Layout Wrapper (Updated to Split Card)
           ============================================ */
        .login-form {
            position: relative;
            width: 100%;
            max-width: 900px;
            z-index: 10;
            display: flex;
            flex-direction: row;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            box-shadow:
                0 20px 40px -10px rgba(0, 0, 0, 0.25),
                0 10px 20px -5px rgba(0, 0, 0, 0.15),
                inset 0 1px 1px rgba(255, 255, 255, 1);
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        /* --- Left Side: Branding & Animation --- */
        .split-left {
            flex: 1;
            background: var(--bg-base);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .split-left h2 {
            font-family: 'Impact', sans-serif;
            font-size: 48px;
            letter-spacing: 3px;
            color: #ffffff;
            margin-bottom: 10px;
            text-align: center;
            line-height: 1;
        }

        .split-left p {
            color: #94a3b8;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        /* --- Right Side: Existing Login Card --- */
        .login-card {
            flex: 1;
            position: relative;
            padding: 48px 36px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        /* ============================================
               Responsive Mobile Adjustments
           ============================================ */
        @media (max-width: 768px) {
            /* Switch to a stacked layout */
            .login-form {
                flex-direction: column;
                max-width: 480px; /* Perfect width for mobile */
                margin: 20px auto;
            }
            .split-left {
                padding: 30px 20px;
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .split-left h2 {
                font-size: 36px;
                margin-bottom: 5px;
            }
            .split-left p {
                margin-bottom: 20px; /* Reduced gap so the form stays visible */
            }
            .login-card {
                padding: 36px 24px; /* Tighter padding for mobile */
            }
            /* Slightly shrink the particle animation to save screen space */
            .particle-system {
                --uib-size: 55px !important;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px; /* Give the card more room on tiny screens */
            }
            .login-card {
                padding: 30px 20px;
            }
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
            padding: 12px 14px 12px 42px;
            font-family: inherit;
            font-size: 15px;
            color: var(--bg-base);
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .login-card input::placeholder {
            color: #94a3b8;
        }

        .login-card input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(30, 96, 145, 0.15);
        }

        .input-wrapper:focus-within i.icon-left {
            color: var(--accent-blue);
        }

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

        .login-form button[type="submit"],
        .primary-btn {
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

        .login-form button[type="submit"]:hover,
        .primary-btn:hover {
            transform: translateY(-2px);
            background: #164a72;
            box-shadow: 0 6px 20px rgba(30, 96, 145, 0.5);
        }

        .login-form button[type="submit"]:active,
        .primary-btn:active {
            transform: translateY(0);
        }

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

        /* ============================================
               Particle Animation (Left Card)
           ============================================ */
        .particle-system {
            --uib-size: 70px;
            --uib-color: #ffffff;
            --uib-speed: 1.75s;
            position: relative;
            height: var(--uib-size);
            width: var(--uib-size);
            animation: rotate calc(var(--uib-speed) * 4) linear infinite;
        }

        .particle {
            position: absolute;
            top: 0%;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .particle:nth-child(1) { --uib-delay: 0; transform: rotate(8deg); }
        .particle:nth-child(2) { --uib-delay: -0.4; transform: rotate(36deg); }
        .particle:nth-child(3) { --uib-delay: -0.9; transform: rotate(72deg); }
        .particle:nth-child(4) { --uib-delay: -0.5; transform: rotate(90deg); }
        .particle:nth-child(5) { --uib-delay: -0.3; transform: rotate(144deg); }
        .particle:nth-child(6) { --uib-delay: -0.2; transform: rotate(180deg); }
        .particle:nth-child(7) { --uib-delay: -0.6; transform: rotate(216deg); }
        .particle:nth-child(8) { --uib-delay: -0.7; transform: rotate(252deg); }
        .particle:nth-child(9) { --uib-delay: -0.1; transform: rotate(300deg); }
        .particle:nth-child(10) { --uib-delay: -0.8; transform: rotate(324deg); }
        .particle:nth-child(11) { --uib-delay: -1.2; transform: rotate(335deg); }
        .particle:nth-child(12) { --uib-delay: -0.5; transform: rotate(290deg); }
        .particle:nth-child(13) { --uib-delay: -0.2; transform: rotate(240deg); }

        .particle::before {
            content: '';
            position: absolute;
            height: 17.5%;
            width: 17.5%;
            border-radius: 50%;
            background-color: var(--uib-color);
            flex-shrink: 0;
            transition: background-color 0.3s ease;
            --uib-d: calc(var(--uib-delay) * var(--uib-speed));
            animation: orbit var(--uib-speed) linear var(--uib-d) infinite;
        }

        @keyframes orbit {
            0% { transform: translate(calc(var(--uib-size) * 0.5)) scale(0.73684); opacity: 0.65; }
            5% { transform: translate(calc(var(--uib-size) * 0.4)) scale(0.684208); opacity: 0.58; }
            10% { transform: translate(calc(var(--uib-size) * 0.3)) scale(0.631576); opacity: 0.51; }
            15% { transform: translate(calc(var(--uib-size) * 0.2)) scale(0.578944); opacity: 0.44; }
            20% { transform: translate(calc(var(--uib-size) * 0.1)) scale(0.526312); opacity: 0.37; }
            25% { transform: translate(0%) scale(0.47368); opacity: 0.3; }
            30% { transform: translate(calc(var(--uib-size) * -0.1)) scale(0.526312); opacity: 0.37; }
            35% { transform: translate(calc(var(--uib-size) * -0.2)) scale(0.578944); opacity: 0.44; }
            40% { transform: translate(calc(var(--uib-size) * -0.3)) scale(0.631576); opacity: 0.51; }
            45% { transform: translate(calc(var(--uib-size) * -0.4)) scale(0.684208); opacity: 0.58; }
            50% { transform: translate(calc(var(--uib-size) * -0.5)) scale(0.73684); opacity: 0.65; }
            55% { transform: translate(calc(var(--uib-size) * -0.4)) scale(0.789472); opacity: 0.72; }
            60% { transform: translate(calc(var(--uib-size) * -0.3)) scale(0.842104); opacity: 0.79; }
            65% { transform: translate(calc(var(--uib-size) * -0.2)) scale(0.894736); opacity: 0.86; }
            70% { transform: translate(calc(var(--uib-size) * -0.1)) scale(0.947368); opacity: 0.93; }
            75% { transform: translate(0%) scale(1); opacity: 1; }
            80% { transform: translate(calc(var(--uib-size) * 0.1)) scale(0.947368); opacity: 0.93; }
            85% { transform: translate(calc(var(--uib-size) * 0.2)) scale(0.894736); opacity: 0.86; }
            90% { transform: translate(calc(var(--uib-size) * 0.3)) scale(0.842104); opacity: 0.79; }
            95% { transform: translate(calc(var(--uib-size) * 0.4)) scale(0.789472); opacity: 0.72; }
            100% { transform: translate(calc(var(--uib-size) * 0.5)) scale(0.73684); opacity: 0.65; }
        }

        /* ============================================
               Global Loader
           ============================================ */
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

        .goldtown-label {
            font-family: "Impact", sans-serif;
            color: hsl(0, 57%, 95%);
            font-size: 42px;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1;
        }

        /* Loading progress bar below spinner */
        .container {
            --uib-size: 150px;
            --uib-color: rgb(238, 243, 254);
            --uib-speed: 0.5s;
            --uib-stroke: 5px;
            --uib-bg-opacity: 0.1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: var(--uib-stroke);
            width: var(--uib-size);
            border-radius: calc(var(--uib-stroke) / 2);
            overflow: hidden;
            transform: translate3d(0, 0, 0);
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: var(--uib-color);
            opacity: var(--uib-bg-opacity);
            transition: background-color 0.3s ease;
        }

        .container::after {
            content: "";
            height: 100%;
            width: 100%;
            border-radius: calc(var(--uib-stroke) / 2);
            animation: zoom var(--uib-speed) ease-in-out infinite;
            transform: translateX(-100%);
            background-color: var(--uib-color);
            transition: background-color 0.3s ease;
        }

        @keyframes zoom {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
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
    </style>
</head>

<body>
    <div id="toast" class="toast-notification">
        <i class="fa-solid fa-check-circle" style="color: #4ade80;"></i>
        <span>Link copied to clipboard!</span>
    </div>

    <div id="global-loader">
        <div class="loader-content">
            <h1 class="goldtown-label">GOLDTOWN</h1>
            <div class="container"></div>
        </div>
    </div>

    <div class="login-form">
        
        <div class="split-left">
            <h2>GOLDTOWN</h2>
            <p>Inventory Tracking</p>
            
            <div class="particle-system">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
        </div>

        <div class="login-card">
            <button class="share-btn" id="shareBtn" aria-label="Share this page">
                <i class="fa-solid fa-share-nodes"></i>
            </button>

            <img class="logo" src="/storage/img/login-logo.png" alt="login logo">

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
                if (navigator.share && navigator.canShare(shareData)) {
                    await navigator.share(shareData);
                } else {
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

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>

</html>
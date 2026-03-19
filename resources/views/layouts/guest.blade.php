<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Goldtown | Inventory Tracking</title>
        <link rel="icon" type="image/x-icon" href="storage/img/goldtown2.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Poppins', Arial, Helvetica, sans-serif;
                background-color: #0d324d;

                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                color: #1f2937;
                overflow: hidden;
            }

            /* floating squares container */
            .squares {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 0;
                
                
            }

            /* square bubbles */
            .squares span {
                position: absolute;
                display: block;
                background: rgba(255,255,255,0.15);
                animation: floatSquare 20s linear infinite;
                bottom: -150px;.
                color: rgb(255, 255, 255);
                background-image:url('storage/img/logo.png');
            }

            .squares span:nth-child(1) { left: 10%; width: 40px; height: 40px; animation-duration: 15s; }
            .squares span:nth-child(2) { left: 20%; width: 80px; height: 80px; animation-duration: 18s; }
            .squares span:nth-child(3) { left: 35%; width: 25px; height: 25px; animation-duration: 12s; }
            .squares span:nth-child(4) { left: 50%; width: 100px; height: 100px; animation-duration: 25s; }
            .squares span:nth-child(5) { left: 65%; width: 50px; height: 50px; animation-duration: 17s; }
            .squares span:nth-child(6) { left: 80%; width: 70px; height: 70px; animation-duration: 22s; }
            .squares span:nth-child(7) { left: 90%; width: 30px; height: 30px; animation-duration: 14s; }

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

            .login-card {
                width: 100%;
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                background-color: rgb(255, 255, 255);
                border: 1px solid rgba(255, 255, 255, 0.4);
                border-radius: 10px;
                padding: 32px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                text-align: center;
                animation: slideRight 0.6s ease-out;
            }

            .logo {
                width: 150px;
                height: auto;
                object-fit: contain;
                margin-bottom: 24px;
                cursor: pointer;
            }

            .logo:hover{
                transform: translateY(-2px)
            }

            .login-form button {
                width: 100%;
                padding: 12px;
                margin-top: 16px;
                background: #0d324d;
                color: #ffffff;
                font-family: 'Poppins', sans-serif;
                font-size: 15px;
                font-weight: 600;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
                box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
            }

            .login-form button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(6, 182, 212, 0.4);
            }

            .login-form button:active {
                transform: translateY(0);
            }
            .login-card form {
                display: flex;
                flex-direction: column;
                gap: 15px;
                text-align: left;
                width: 100%;
            }

            .form-group {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .login-card input {
                width: 100%;
                padding: 10px 14px 12px 40px;
                font-family: 'Poppins', sans-serif;
                font-size: 12px;
                color: #1f2937;
                background-color: rgba(255, 255, 255, 0.3);
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                transition: all 0.2s ease;
            }
            .login-card label {
                text-align: center;
            }

            .login-card a {
                color: rgb(37, 104, 109);
                font-size: 15px;
            }

           .input-error {
                color: #dc3545;
                font-size: 13px;
                margin-top: 4px;
                font-weight: 500;
            }

            @keyframes slideRight {
                from { opacity: 0; transform: translateX(20px); }
                to { opacity: 1; transform: translateX(0); }
            }

            @media (max-width: 480px) {
                .login-card {
                    padding: 24px;
                    border-radius: 16px;
                }
            }

            @keyframes floatSquare {
                0% { transform: translateY(0) rotate(0deg); opacity: 0; }
                50% { opacity: 1; }
                100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
            }

            #global-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: #0d324d; 
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: transform 0.6s cubic-bezier(0.86, 0, 0.07, 1);
            }

            #global-loader.slide-up {
                transform: translateY(-100%);
            }

            /* Wrapper to stack the spinner, image, and text vertically */
            .loader-content {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }

            /* Loader wrapper for stacking image and spinner */
            .loader-wrapper {
                position: relative;
                width: 150px;  /* Bigger spinner area */
                height: 150px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /* Center image */
            .loader-image {
                width: 250px; /* Larger image */
                height: 250px;
                object-fit: contain;
                animation: imagePulse 2s infinite ease-in-out;
            }

            /* Loading Text */
            .loading-text {
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                font-weight: 500;
                color: #ffffff;
                letter-spacing: 2px;
                animation: textPulse 2s infinite ease-in-out;
                margin: 0;
            }

            @keyframes loaderSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
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
        <div id="global-loader">
            <div class="loader-content">
                <div class="loader-wrapper">
                    <div class="loader-spinner"></div>
                    <img src="storage/img/dawg.gif" alt="Loading" class="loader-image">
                </div>
                <p class="loading-text">LOADING...</p>
            </div>
        </div>

        <div class="login-form">
            <div class="login-card">
                <img class="logo" src="storage/img/login-logo.png" alt="login logo">
                
                {{$slot}}
                
            </div>
        </div>
        
        <div class="squares">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <script>
            // Wait for the page to fully load
            window.addEventListener('load', () => {
                const loader = document.getElementById('global-loader');
                if (loader) {
                    // Small delay makes sure the user sees the loader before it slides up
                    setTimeout(() => {
                        loader.classList.add('slide-up');
                    }, 400); 
                }
            });
        </script>
    </body>
</html>
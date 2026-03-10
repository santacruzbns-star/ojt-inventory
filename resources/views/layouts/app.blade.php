<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
             /* ============================================
               Global Slide-Up Loading Screen
               ============================================ */
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
    <body class="font-sans antialiased">
         <div id="global-loader">
            <div class="loader-content">
                <div class="loader-wrapper">
                    <div class="loader-spinner"></div>
                    <img src="storage/img/dawg.gif" alt="Loading" class="loader-image">
                </div>
                <p class="loading-text">LOADING...</p>
            </div>
        </div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
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
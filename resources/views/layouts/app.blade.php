<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Goldtown | Inventory Tracking</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600&display=swap" rel="stylesheet" />

        <style>
            /* ============================================
               CSS Reset & Base Variables
               ============================================ */
            :root {
                --primary-color: #0d324d;
                --text-color: #333333;
                --bg-color: #f8f9fa;
                --white: #ffffff;
                --transition-speed: 0.3s;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background-color: var(--bg-color);
                color: var(--text-color);
                /* Ensure body takes full height for sticky footer */
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            /* ============================================
               Global Slide-Up Loading Screen (Your existing code)
               ============================================ */
            #global-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: var(--primary-color); 
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
                font-family: 'Poppins', sans-serif;
                font-size: 16px;
                font-weight: 500;
                color: var(--white);
                letter-spacing: 2px;
                animation: textPulse 2s infinite ease-in-out;
                margin: 0;
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

            /* ============================================
               Header / Navigation
               ============================================ */
            .modern-header {
                background-color: var(--white);
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .nav-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 70px;
            }

            .logo {
                font-family: 'Poppins', sans-serif;
                font-size: 22px;
                font-weight: 600;
                color: var(--primary-color);
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .nav-menu {
                display: flex;
                list-style: none;
                gap: 30px;
            }

            .nav-link {
                text-decoration: none;
                color: var(--text-color);
                font-weight: 500;
                transition: color var(--transition-speed);
                position: relative;
            }

            .nav-link:hover {
                color: var(--primary-color);
            }

            .nav-link::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: -4px;
                left: 0;
                background-color: var(--primary-color);
                transition: width var(--transition-speed);
            }

            .nav-link:hover::after {
                width: 100%;
            }

            /* Hamburger Menu (Mobile) */
            .hamburger {
                display: none;
                cursor: pointer;
                background: none;
                border: none;
                padding: 5px;
            }

            .hamburger-line {
                display: block;
                width: 25px;
                height: 3px;
                margin: 5px auto;
                background-color: var(--primary-color);
                transition: all var(--transition-speed);
            }

            /* ============================================
               Main Content Area
               ============================================ */
            .main-content {
                flex: 1; /* Pushes footer to the bottom */
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
                padding: 40px 20px;
            }

            /* ============================================
               Footer
               ============================================ */
            .modern-footer {
                background-color: var(--primary-color);
                color: var(--white);
                padding: 30px 20px;
                text-align: center;
                margin-top: auto; /* Ensures it stays at the bottom */
            }

            .footer-content {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .footer-links {
                display: flex;
                justify-content: center;
                gap: 20px;
                list-style: none;
                margin-top: 10px;
            }

            .footer-links a {
                color: var(--white);
                text-decoration: none;
                font-size: 14px;
                opacity: 0.8;
                transition: opacity var(--transition-speed);
            }

            .footer-links a:hover {
                opacity: 1;
            }

            .footer-copy {
                font-size: 14px;
                opacity: 0.7;
                margin-top: 15px;
            }

            /* ============================================
               Responsive Design
               ============================================ */
            @media (max-width: 768px) {
                .hamburger {
                    display: block;
                }

                .nav-menu {
                    position: absolute;
                    top: 70px;
                    left: -100%;
                    flex-direction: column;
                    background-color: var(--white);
                    width: 100%;
                    text-align: center;
                    transition: 0.3s;
                    box-shadow: 0 10px 10px rgba(0,0,0,0.05);
                    padding: 20px 0;
                    gap: 0;
                }

                .nav-menu.active {
                    left: 0;
                }

                .nav-menu li {
                    margin: 15px 0;
                }

                /* Hamburger Animation */
                .hamburger.active .hamburger-line:nth-child(1) {
                    transform: translateY(8px) rotate(45deg);
                }
                .hamburger.active .hamburger-line:nth-child(2) {
                    opacity: 0;
                }
                .hamburger.active .hamburger-line:nth-child(3) {
                    transform: translateY(-8px) rotate(-45deg);
                }
            }
        </style>
    </head>
    <body>
        <div id="global-loader">
            <div class="loader-content">
                <div class="loader-wrapper">
                    <img src="{{ asset('storage/img/dawg.gif') }}" alt="Loading" class="loader-image">
                </div>
                <p class="loading-text">LOADING...</p>
            </div>
        </div>

        <header class="modern-header">
            <nav class="nav-container">
                <a href="/" class="logo">
                    Goldtown
                </a>
                
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link">Dashboard</a></li>
                    <li><a href="/inventory" class="nav-link">Inventory</a></li>
                    <li><a href="/reports" class="nav-link">Reports</a></li>
                    <li><a href="/settings" class="nav-link">Settings</a></li>
                </ul>

                <button class="hamburger" aria-label="Toggle Navigation">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </nav>
        </header>

        <main class="main-content">
            @yield('content')
        </main>

        <footer class="modern-footer">
            <div class="footer-content">
                <h3>Goldtown Inventory</h3>
                <p style="font-size: 14px; opacity: 0.8;">Streamlining your tracking process.</p>
                <ul class="footer-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
                <div class="footer-copy">
                    &copy; {{ date('Y') }} Goldtown. All rights reserved.
                </div>
            </div>
        </footer>

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

            // Mobile Menu Toggle Logic
            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".nav-menu");
            const navLinks = document.querySelectorAll(".nav-link");

            hamburger.addEventListener("click", () => {
                hamburger.classList.toggle("active");
                navMenu.classList.toggle("active");
            });

            // Close mobile menu when a link is clicked
            navLinks.forEach(link => {
                link.addEventListener("click", () => {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                });
            });
        </script>
    </body>
</html>
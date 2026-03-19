<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Goldtown | Inventory Tracking</title>
        <link rel="icon" type="image/x-icon" href="storage/img/goldtown2.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600&display=swap" rel="stylesheet" />

        <style>
            /* ============================================
               CSS Reset & Base Variables
               ============================================ */
            :root {
                --primary-color: #0d324d;
                --primary-hover: #082235;
                --text-color: #333333;
                --bg-color: #f8f9fa;
                --white: #ffffff;
                --transition-speed: 0.3s;
                /* CHANGED: Swapped 1700px for 100% to make the layout full-width */
                --max-layout-width: 100%; 
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'poppins', Arial, Helvetica, sans-serif;
                background-color: var(--bg-color);
                color: var(--text-color);
                font-size: 18px; 
                min-height: 950px; 
                display: flex;
                flex-direction: column;
                margin: 0 auto;
            }

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
                font-size: 20px; 
                font-weight: 600;
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
               Header & Navigation
               ============================================ */
            .modern-header {
                background-color: var(--white);
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .nav-container {
                max-width: var(--max-layout-width);
                margin: 0 auto;
                padding: 0 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 100px; /* Taller to fit the larger logo */
            }

            .nav-logo {
                display: flex;
                align-items: center;
            }

           .nav-logo img {
                height: 90px;
                width: auto;
                object-fit: contain;
                position: fixed;
                top: 10px;     /* distance from top */
                left: 30px;    /* distance from left */
                z-index: 1000; /* keeps it above other elements */
            }

            .nav-menu {
                display: flex;
                align-items: center;
                list-style: none;
                gap: 35px; 
                margin: 0;
            }

            .nav-link {
                text-decoration: none;
                color: var(--text-color);
                font-weight: 500;
                font-size: 1.1rem; 
                transition: color var(--transition-speed);
                position: relative;
                padding: 5px 0;
            }

            .nav-link:hover, .nav-link.active {
                color: var(--primary-color);
            }

            .nav-link::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: 0;
                left: 0;
                background-color: var(--primary-color);
                transition: width var(--transition-speed);
            }

            .nav-link:hover::after, .nav-link.active::after {
                width: 100%;
            }

            .logout-item {
                display: flex;
                align-items: center;
            }

            .logout-btn {
                background-color: var(--primary-color);
                color: var(--white);
                border: none;
                padding: 10px 22px; 
                border-radius: 6px;
                font-family: inherit;
                font-size: 1.05rem; 
                font-weight: 600;
                cursor: pointer;
                transition: background-color var(--transition-speed);
            }

            .logout-btn:hover {
                background-color: var(--primary-hover);
            }

            /* Hamburger Menu (Mobile) */
            .header-actions {
                display: none; 
            }

            .hamburger {
                cursor: pointer;
                background: none;
                border: none;
                padding: 5px;
            }

            .hamburger-line {
                display: block;
                width: 30px; 
                height: 3px;
                margin: 6px auto;
                background-color: var(--primary-color);
                transition: all var(--transition-speed);
            }

            /* ============================================
               Main Content Area
               ============================================ */
            .main-content {
                flex: 1; 
                max-width: var(--max-layout-width);
                margin: 0 auto;
                width: 100%;
                padding: 40px 99px 120px 99px; 
            }

            /* ============================================
               Footer
               ============================================ */
            .modern-footer {
                background-color: var(--primary-color);
                color: var(--white);
                padding: 20px 5px;
                text-align: center;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
            }

            .footer-content {
                max-width: var(--max-layout-width);
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .footer-content h4 {
                font-size: 1.2rem; 
                margin: 0;
            }

            .footer-copy {
                font-size: 14px; 
                opacity: 0.8;
                margin-top: 5px;
            }

            /* ============================================
               Responsive Design
               ============================================ */
            @media (max-width: 768px) {
                body {
                    min-height: 100vh;
                }

                .header-actions {
                    display: flex;
                    align-items: center;
                }

                .nav-menu {
                    position: absolute;
                    top: 100px; /* Matched to the new taller header height */
                    left: -100%;
                    flex-direction: column;
                    background-color: var(--white);
                    width: 100%;
                    text-align: center;
                    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    box-shadow: 0 10px 10px rgba(0,0,0,0.05);
                    padding: 30px 0; 
                    gap: 0;
                }

                .nav-menu.active {
                    left: 0;
                }

                .nav-menu li {
                    margin: 20px 0; 
                }

                .logout-item {
                    justify-content: center;
                    margin-top: 15px;
                }

                .logout-btn {
                    width: 80%; 
                    padding: 12px;
                    font-size: 1.1rem;
                }

                /* Hamburger Animation */
                .hamburger.active .hamburger-line:nth-child(1) {
                    transform: translateY(9px) rotate(45deg);
                }
                .hamburger.active .hamburger-line:nth-child(2) {
                    opacity: 0;
                }
                .hamburger.active .hamburger-line:nth-child(3) {
                    transform: translateY(-9px) rotate(-45deg);
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
                
                <a href="/" class="nav-logo">
                    <img src="{{ asset('storage/img/login-logo.png') }}" alt="Goldtown Logo">
                </a>
                
                <ul class="nav-menu">
                    <li><a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    <li><a href="/inventory" class="nav-link {{ request()->is('inventory') ? 'active' : '' }}">Inventory</a></li>
                    <li><a href="/outbound" class="nav-link {{ request()->is('outbound') ? 'active' : '' }}">Issued Items</a></li>
                    <li><a href="/settings" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">Settings</a></li>
                    
                    <li class="logout-item">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
                            @csrf

                          <button type="button" class="logout-btn" onclick="confirmLogout()">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>

                <div class="header-actions">
                    <button class="hamburger" aria-label="Toggle Navigation">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>

            </nav>
        </header>

        <main class="main-content">
           {{$slot}}
        </main>

        <footer class="modern-footer">
            <div class="footer-content">
                <h4>Goldtown Inventory</h4>
                <div class="footer-copy">
                    &copy; 2026 Goldtown. All rights reserved.
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

            navLinks.forEach(link => {
                link.addEventListener("click", () => {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                });
            });

            function confirmLogout() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out of your session.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d324d', // Matches your primary color
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, logout',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form if the user clicks 'Yes'
                        document.getElementById('logout-form').submit();
                    }
                })
            }

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Trigger Toasts based on Laravel Session variables
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{{ session('warning') }}"
                });
            @endif

            @if(session('info'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('info') }}"
                });
            @endif
        </script>
    </body>
</html>
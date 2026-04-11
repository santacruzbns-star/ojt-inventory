<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Goldtown | Inventory Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="storage/img/goldtown2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="storage/css/app.css">
</head>
<style>
    /* ============================================
       CSS Reset & Base Variables
       ============================================ */
    :root {
        --primary-color: #0d324d; /* Base dark navy */
        --primary-hover: #082235;
        --active-blue: #0d6efd;
        --accent-color: #d4af37;
        --text-color: #2b2b2b;
        --text-muted: #6c757d;
        --bg-color: #f4f6f9;
        --white: #ffffff;
        --transition-speed: 0.3s;
        --max-layout-width: 100%;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', Arial, Helvetica, sans-serif;
        background-color: var(--bg-color);
        color: var(--text-color);
        font-size: 16px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
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

    .loader-image {
        width: 250px;
        height: 250px;
        object-fit: contain;
        animation: imagePulse 2s infinite ease-in-out;
    }

    .loading-text {
        font-size: 20px;
        font-weight: 600;
        color: var(--white);
        letter-spacing: 3px;
        animation: textPulse 2s infinite ease-in-out;
        margin: 0;
    }

    @keyframes imagePulse {
        0%, 100% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
    }

    @keyframes textPulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }

    /* ============================================
       Modern Header & Navigation
       ============================================ */
    .modern-header {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
    }

    .nav-container {
        max-width: var(--max-layout-width);
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 85px;
    }

    .nav-logo {
        display: flex;
        align-items: center;
        transition: transform var(--transition-speed);
    }

    .nav-logo:hover {
        transform: scale(1.02);
    }

    .nav-logo img {
        height: 80px;
        width: auto;
        object-fit: contain;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        list-style: none;
        gap: 15px;
        margin: 0;
    }

    .nav-link {
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 500;
        font-size: 1rem;
        padding: 10px 18px;
        border-radius: 8px;
        transition: all var(--transition-speed) ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-link i {
        font-size: 1.1rem;
        display: inline-block; 
        transition: color var(--transition-speed);
        transform-origin: center;
    }

    /* Blue Hover & Active States */
    .nav-link:hover,
    .nav-link.active {
        color: var(--active-blue);
        background-color: rgba(13, 110, 253, 0.08);
    }

    .nav-link.active {
        font-weight: 600;
    }

    .nav-link:hover i,
    .nav-link.active i {
        color: var(--active-blue);
    }

    /* ============================================
       Custom Icon Animations (HOVER ONLY)
       ============================================ */
       
    /* 1. Dashboard (Grid) - Smooth Pulse */
    @keyframes pulseDashboard {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    .nav-link:hover .bi-grid {
        animation: pulseDashboard 1.2s ease-in-out infinite;
    }

    /* 2. Inventory (Box) - "Pop Open" Squish & Stretch */
    @keyframes openBox {
        0% { transform: scale(1) translateY(0); }
        30% { transform: scaleX(1.2) scaleY(0.8) translateY(2px); } /* Squish down */
        60% { transform: scaleX(0.9) scaleY(1.2) translateY(-6px); } /* Burst up */
        80% { transform: scaleX(1.05) scaleY(0.95) translateY(-4px); } /* Recoil */
        100% { transform: scale(1.1) translateY(-5px); } /* Settle in open/popped state */
    }
    .nav-link:hover .bi-box-seam {
        animation: openBox 0.5s cubic-bezier(0.28, 0.84, 0.42, 1) forwards;
    }

    /* 3. Outbound (Truck) - Driving Animation */
    @keyframes driveTruck {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(3px) translateY(-1px); }
        75% { transform: translateX(1px) translateY(1px); }
    }
    .nav-link:hover .bi-truck {
        animation: driveTruck 1s linear infinite;
    }

    /* 4. Return (Arrow) - Swoop Back Animation */
    @keyframes returnSwoop {
        0%, 100% { transform: translateX(0) scale(1); }
        50% { transform: translateX(-4px) scale(1.1); }
    }
    .nav-link:hover .bi-arrow-return-left {
        animation: returnSwoop 0.8s ease-in-out infinite;
    }

    /* 5. Profile (Gear) - Spinning Animation */
    @keyframes spinGear {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(180deg); }
    }
    .nav-link:hover .bi-gear {
        animation: spinGear 2s linear infinite;
    }

    /* Logout Button */
    .logout-item {
        margin-left: 15px;
    }

    .btn-logout {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all var(--transition-speed);
    }
    
    .btn-logout i {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .btn-logout:hover i {
        transform: translateX(4px);
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
        width: 28px;
        height: 3px;
        margin: 5px auto;
        background-color: var(--primary-color);
        border-radius: 3px;
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
        padding: 40px 5%;
    }

    /* ============================================
       Modern Footer with Grid Pattern
       ============================================ */
    .modern-footer {
        background-color: #0b253a; 
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 40px 40px; 
        
        color: var(--white);
        padding: 10px 5px;
        text-align: center;
        width: 100%;
        margin-top: auto;
        border-top: 3px solid var(--accent-color);
    }

    .footer-content {
        max-width: var(--max-layout-width);
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .footer-content h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        letter-spacing: 1px;
    }

    .footer-copy {
        font-size: 0.9rem;
        opacity: 0.7;
    }

    /* ============================================
       Responsive Design
       ============================================ */
    @media (max-width: 992px) {
        .nav-container { padding: 0 20px; }
        .nav-menu { gap: 5px; }
        .nav-link { padding: 8px 12px; }
    }

    @media (max-width: 768px) {
        .header-actions {
            display: flex;
            align-items: center;
        }

        .nav-menu {
            position: absolute;
            top: 85px;
            left: -100%;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            width: 100%;
            text-align: center;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            gap: 10px;
        }

        .nav-menu.active {
            left: 0;
        }

        .nav-link {
            justify-content: center;
            border-radius: 0;
            padding: 15px;
        }

        .logout-item {
            margin: 15px 0;
            display: flex;
            justify-content: center;
        }

        .btn-logout {
            width: 80%;
            justify-content: center;
        }

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
                <li>
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/inventory" class="nav-link {{ request()->is('inventory') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Inventory
                    </a>
                </li>
                <li>
                    <a href="/outbound" class="nav-link {{ request()->is('outbound') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Issued Items
                    </a>
                </li>
                <li>
                    <a href="/return" class="nav-link {{ request()->is('return') ? 'active' : '' }}">
                        <i class="bi bi-arrow-return-left"></i> Returned Items
                    </a>
                </li>
                <li>
                    <a href="/profile" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="logout-item">
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="button" class="btn btn-outline-danger btn-logout" onclick="confirmLogout()">
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
        {{ $slot }}
    </main>

    <footer class="modern-footer">
        <div class="footer-content">
            <h4>Goldtown Inventory</h4>
            <div class="footer-copy">
                &copy; {{ date('Y') }} Goldtown. All rights reserved.
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

        // SweetAlert Logout
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your session.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-sign-out-alt"></i> Yes, logout',
                cancelButtonText: '<i class="fa fa-times"></i> Cancel',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Global Toast Notification Configuration
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
        @if (session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif

        @if (session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif

        @if (session('warning'))
            Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
        @endif

        @if (session('info'))
            Toast.fire({ icon: 'info', title: "{{ session('info') }}" });
        @endif
    </script>

</body>
</html>
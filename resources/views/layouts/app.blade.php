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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="storage/css/app.css">
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
                <li>
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-collection"></i>
                        Dashboard
                    </a>
                </li>
                <li><a href="/inventory" class="nav-link {{ request()->is('inventory') ? 'active' : '' }}"><i
                            class="bi bi-box-seam"></i>
                        Inventory
                    </a>
                </li>
                <li><a href="/outbound" class="nav-link {{ request()->is('outbound') ? 'active' : '' }}"><i
                            class="bi bi-minecart"></i>
                        Issued Items</a>
                </li>
                <li><a href="/profile" class="nav-link {{ request()->is('profile') ? 'active' : '' }}"><i
                            class="bi bi-gear"></i>
                        Settings</a>
                </li>
                <li class="logout-item">
                    <form id="logout-form" method="POST" action="{{ route('logout') }}"
                        style="margin: 0; width: 100%;">
                        @csrf

                        <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
                            <i class="bi bi-door-open"></i> Logout
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
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Goldtown | Inventory Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/storage/img/goldtown2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="storage/css/app.css">
</head>
<style>

</style>

<body>
    <div id="global-loader">
    <div class="loader-content">
        <h1 class="goldtown-label">GOLDTOWN</h1>
        <div class="loader-bar"></div> 
    </div>
</div>

    <header class="modern-header">
        <nav class="nav-container">
            <a href="/dashboard" class="nav-logo">
                <img src="{{ asset('/storage/img/login-logo.png') }}" alt="Goldtown Logo">
            </a>

            <ul class="nav-menu">
    <li>
        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </li>
    <li>
        <a href="/inventory" class="nav-link {{ request()->is('inventory') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Inventory Management
        </a>
    </li>
    <li>
        <a href="/outbound" class="nav-link {{ request()->is('outbound') ? 'active' : '' }}">
            <i class="bi bi-repeat"></i> Item Dispatch
        </a>
    </li>
    <li>
        <a href="/return" class="nav-link {{ request()->is('return') ? 'active' : '' }}">
           <i class="bi bi-box-arrow-in-down-left"></i> Item Returns
        </a>
    </li>
    <li>
        <a href="/profile" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Account Settings
        </a>
    </li>
    <li class="logout-item">
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="button" class="btn btn-outline-danger btn-logout" onclick="confirmLogout()">
                <i class="bi bi-box-arrow-right"></i> Sign Out
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
    <script>
    // This event fires the exact millisecond the browser finishes loading all page assets
    window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        const content = document.getElementById('main-content');
        
        // 1. Trigger the CSS slide-up animation immediately
        loader.classList.add('slide-up');
        
        // 2. Show your main content
        content.style.display = 'block';
        
        // 3. (Optional but recommended) Remove the loader from the HTML completely 
        // after the 0.6s CSS transition finishes so it doesn't block clicks
        setTimeout(() => {
            loader.remove();
        }, 600);
    });
</script>
</body>

</html>

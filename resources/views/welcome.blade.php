<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'GoldTown') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            /* Deep Blue Theme Colors */
            --bg-base: #0d324d; 
            --bg-darker: #092438;
            --accent-blue: #1e6091; 
            --accent-green: #7e9c65; 
            --text-main: #ffffff;
            --text-light: rgba(255, 255, 255, 0.7);
            --border-light: rgb(255, 255, 255);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            
            /* Modern Box Grid Background */
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: center center;
            background-attachment: fixed;
        }

        /* NAVBAR */
        .navbar {
            padding: 1.5rem 0;
            background: transparent;
            transition: all 0.4s ease;
            border-bottom: 1px solid transparent;
        }

        .navbar.scrolled {
            background: rgba(13, 50, 77, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.75rem;
            color: var(--text-main) !important;
        }

        .nav-link {
            color: var(--text-main) !important;
            font-weight: 600;
            margin-left: 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-green) !important;
        }

        /* HERO SECTION */
        .hero-section {
            padding: 140px 0 100px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-size: clamp(2.5rem, 4.5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 3rem;
            max-width: 90%;
        }

        /* BUTTONS */
        .btn-custom {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-custom {
            background-color: var(--accent-blue);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 96, 145, 0.4);
        }

        .btn-primary-custom:hover {
            background-color: #164a72;
            transform: translateY(-3px);
            color: white;
            box-shadow: 0 6px 20px rgba(30, 96, 145, 0.6);
        }

        .btn-secondary-custom {
            background-color: var(--accent-green);
            color: white;
            box-shadow: 0 4px 15px rgba(126, 156, 101, 0.3);
        }

        .btn-secondary-custom:hover {
            background-color: #6a8553;
            transform: translateY(-3px);
            color: white;
            box-shadow: 0 6px 20px rgba(126, 156, 101, 0.5);
        }

        /* IMAGE COMPOSITION */
        .image-composition {
            position: relative;
            height: 550px;
            width: 100%;
        }

        .img-box {
            position: absolute;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: var(--bg-base);
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .img-box:hover {
            transform: translateY(-10px);
            z-index: 10 !important;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .img-box:hover img {
            transform: scale(1.05);
        }

        .img-main { top: 15%; left: 5%; width: 60%; height: 70%; z-index: 2; border: 2px solid var(--accent-blue); }
        .img-top-right { top: 0; right: 0; width: 45%; height: 45%; z-index: 1; }
        .img-bottom-right { bottom: 5%; right: 5%; width: 50%; height: 40%; z-index: 3; }

        /* SECTIONS GENERAL */
        .section-padding { padding: 100px 0; }
        .section-title { font-weight: 800; font-size: 2.5rem; margin-bottom: 1rem; letter-spacing: -0.5px; }
        .section-tag { color: var(--accent-green); text-transform: uppercase; font-weight: 700; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 0.5rem; display: block; }

        /* ABOUT SECTION */
        .about-section {
            background: linear-gradient(180deg, transparent, var(--bg-darker));
            border-top: 1px solid var(--border-light);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-5px);
            border-color: var(--accent-blue);
        }

        .feature-icon { font-size: 2rem; color: var(--accent-blue); margin-bottom: 1.5rem; }

        /* DOCUMENTATION ACCORDION */
        .docs-section {
            background-color: var(--bg-darker);
            border-top: 1px solid var(--border-light);
        }

        .accordion-item {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
            margin-bottom: 1rem;
            border-radius: 12px !important;
            overflow: hidden;
        }

        .accordion-button {
            background-color: transparent;
            color: var(--text-main);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(30, 96, 145, 0.15);
            color: var(--text-main);
            border-bottom: 1px solid var(--border-light);
        }

        .accordion-button::after { filter: invert(1); }
        .accordion-body { color: var(--text-light); line-height: 1.7; padding: 1.5rem; }

        /* FOOTER */
        footer { background-color: #061926; border-top: 1px solid var(--border-light); }

        @media (max-width: 991px) {
            .image-composition { height: 400px; margin-top: 3rem; }
            .hero-section { padding: 120px 0 60px; min-height: auto; }
            .navbar { background: rgba(13, 50, 77, 0.95); backdrop-filter: blur(10px); }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                GOLDTOWN
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255,255,255,0.5);">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link px-3" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#docs">Documentation</a></li>
                    
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-custom btn-secondary-custom ms-lg-3">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link px-3">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-custom btn-secondary-custom ms-lg-3">Get Started</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 pe-lg-5" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="hero-title">
                        Goldtown Inventory<br>
                        Management System
                    </h1>
                    <p class="hero-subtitle">
                        Manage and monitor company inventory, stock levels, and product movement in one streamlined, high-performance platform.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#about" class="btn btn-custom btn-primary-custom">Explore Platform</a>
                        <a href="#docs" class="btn btn-custom btn-secondary-custom">Read Docs</a>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="200">
                    <div class="image-composition">
                        <div class="img-box img-top-right">
                            <img src="/storage/img/goldtown3.png" alt="Shipping Containers">
                        </div>
                        <div class="img-box img-main">
                            <img src="/storage/img/gwapa.png" alt="Inventory Manager">
                        </div>
                        <div class="img-box img-bottom-right">
                            <img src="/storage/img/gwapo.png" alt="Warehouse Automation">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="section-padding about-section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6" data-aos="fade-up">
                    <span class="section-tag">About Goldtown</span>
                    <h2 class="section-title">Built for scale, designed for simplicity.</h2>
                    <p class="text-light fs-5 lh-lg">
                        Goldtown was engineered to bridge the gap between complex enterprise resource planning and intuitive, daily operational software. We give warehouse managers and business owners complete visibility into their supply chain without the clutter.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <i class="bi bi-box-seam feature-icon"></i>
                        <h4 class="fw-bold mb-3">Real-time Tracking</h4>
                        <p class="text-light mb-0">Monitor stock levels as they change. Our instantaneous sync ensures you never oversell or run out of critical components.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="bi bi-graph-up-arrow feature-icon"></i>
                        <h4 class="fw-bold mb-3">Advanced Analytics</h4>
                        <p class="text-light mb-0">Generate comprehensive reports on product velocity, seasonal trends, and ROI with just two clicks.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h4 class="fw-bold mb-3">Secure Infrastructure</h4>
                        <p class="text-light mb-0">Enterprise-grade encryption keeps your proprietary supplier data and financial metrics locked down and compliant.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="docs" class="section-padding docs-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">System Documentation</span>
                <h2 class="section-title">How It Works</h2>
                <p class="text-light">Everything you need to know to get started with the Goldtown platform.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="accordion" id="docsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <i class="bi bi-terminal me-2"></i> Initial Setup & Configuration
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#docsAccordion">
                                <div class="accordion-body">
                                    To begin using Goldtown, navigate to your admin dashboard and configure your primary warehouse locations. You can bulk-import existing inventory via a CSV template provided in the settings tab. Ensure all product SKUs are unique before finalizing the import to prevent database conflicts.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <i class="bi bi-upc-scan me-2"></i> Adding New Products
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#docsAccordion">
                                <div class="accordion-body">
                                    Click the <strong>"Add Product"</strong> button on the main dashboard. You will be prompted to enter the Item Name, Category, Base Cost, Retail Price, and Starting Quantity. You can also assign barcode data to enable integration with physical handheld scanners on the warehouse floor.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <i class="bi bi-bell me-2"></i> Setting Low Stock Alerts
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#docsAccordion">
                                <div class="accordion-body">
                                    Goldtown allows you to set automated thresholds. Go to <strong>Inventory > Manage Alerts</strong>. Select a specific product or a whole category, and define the minimum stock level. Once inventory dips below this number, the system will automatically dispatch an email alert to the assigned procurement manager.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <i class="bi bi-people me-2"></i> User Roles & Permissions
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#docsAccordion">
                                <div class="accordion-body">
                                    The system supports three standard roles: <strong>Admin</strong> (full access), <strong>Manager</strong> (can edit stock and view reports), and <strong>Staff</strong> (can only view inventory and process outgoing orders). Roles can be assigned or revoked instantly from the User Management panel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="fs-5 fw-bold text-white letter-spacing-1">GOLDTOWN</span>
                    <p class="text-light mt-2 mb-0 small">
                        &copy; OJT. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-light text-decoration-none me-3 hover-white">Terms of Service</a>
                    <a href="#" class="text-light text-decoration-none me-3 hover-white">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none hover-white">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS Animations
        AOS.init({
            once: true,
            duration: 800,
            offset: 50,
            easing: 'ease-out-cubic'
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>
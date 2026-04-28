<x-app-layout>
    <style>
        /* ============================================
           Page Entrance Animations
           ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-card {
            opacity: 0; /* Start hidden */
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .animate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        /* ============================================
           Jumping Dots Animation 
           (Safely renamed from .container to .matrix-loader)
           ============================================ */
        .matrix-loader {
            --uib-size: 40px; /* Scaled down slightly to fit the header nicely */
            --uib-color: #1e6091; /* Goldtown Accent Blue */
            --uib-speed: 1.5s;
            --uib-dot-size: calc(var(--uib-size) * 0.1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: calc(var(--uib-size) * 0.64);
            width: calc(var(--uib-size) * 0.64);
        }

        @keyframes jump {
            0%, 100% { transform: translateY(120%); }
            50% { transform: translateY(-120%); }
        }

        .matrix-loader .dot {
            --uib-d1: -0.48;
            --uib-d2: -0.4;
            --uib-d3: -0.32;
            --uib-d4: -0.24;
            --uib-d5: -0.16;
            --uib-d6: -0.08;
            --uib-d7: -0;
            position: absolute;
            bottom: calc(var(--uib-bottom) + var(--uib-dot-size) / 2);
            right: calc(var(--uib-right) + var(--uib-dot-size) / 2);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: var(--uib-dot-size);
            width: var(--uib-dot-size);
            animation: jump var(--uib-speed) ease-in-out infinite;
            opacity: var(--uib-scale);
            will-change: transform;
            backface-visibility: hidden;
        }

        .matrix-loader .dot::before {
            content: '';
            height: 100%;
            width: 100%;
            background-color: var(--uib-color);
            border-radius: 50%;
            transform: scale(var(--uib-scale));
            transition: background-color 0.3s ease;
        }

        /* Dot Positions & Delays */
        .matrix-loader .dot:nth-child(1) { --uib-bottom: 24%; --uib-right: -35%; animation-delay: calc(var(--uib-speed) * var(--uib-d1)); }
        .matrix-loader .dot:nth-child(2) { --uib-bottom: 16%; --uib-right: -6%; animation-delay: calc(var(--uib-speed) * var(--uib-d2)); }
        .matrix-loader .dot:nth-child(3) { --uib-bottom: 8%; --uib-right: 23%; animation-delay: calc(var(--uib-speed) * var(--uib-d3)); }
        .matrix-loader .dot:nth-child(4) { --uib-bottom: -1%; --uib-right: 51%; animation-delay: calc(var(--uib-speed) * var(--uib-d4)); }
        .matrix-loader .dot:nth-child(5) { --uib-bottom: 38%; --uib-right: -17.5%; animation-delay: calc(var(--uib-speed) * var(--uib-d2)); }
        .matrix-loader .dot:nth-child(6) { --uib-bottom: 30%; --uib-right: 10%; animation-delay: calc(var(--uib-speed) * var(--uib-d3)); }
        .matrix-loader .dot:nth-child(7) { --uib-bottom: 22%; --uib-right: 39%; animation-delay: calc(var(--uib-speed) * var(--uib-d4)); }
        .matrix-loader .dot:nth-child(8) { --uib-bottom: 14%; --uib-right: 67%; animation-delay: calc(var(--uib-speed) * var(--uib-d5)); }
        .matrix-loader .dot:nth-child(9) { --uib-bottom: 53%; --uib-right: -0.8%; animation-delay: calc(var(--uib-speed) * var(--uib-d3)); }
        .matrix-loader .dot:nth-child(10) { --uib-bottom: 44.5%; --uib-right: 27%; animation-delay: calc(var(--uib-speed) * var(--uib-d4)); }
        .matrix-loader .dot:nth-child(11) { --uib-bottom: 36%; --uib-right: 55.7%; animation-delay: calc(var(--uib-speed) * var(--uib-d5)); }
        .matrix-loader .dot:nth-child(12) { --uib-bottom: 28.7%; --uib-right: 84.3%; animation-delay: calc(var(--uib-speed) * var(--uib-d6)); }
        .matrix-loader .dot:nth-child(13) { --uib-bottom: 66.8%; --uib-right: 15%; animation-delay: calc(var(--uib-speed) * var(--uib-d4)); }
        .matrix-loader .dot:nth-child(14) { --uib-bottom: 58.8%; --uib-right: 43%; animation-delay: calc(var(--uib-speed) * var(--uib-d5)); }
        .matrix-loader .dot:nth-child(15) { --uib-bottom: 50%; --uib-right: 72%; animation-delay: calc(var(--uib-speed) * var(--uib-d6)); }
        .matrix-loader .dot:nth-child(16) { --uib-bottom: 42%; --uib-right: 100%; animation-delay: calc(var(--uib-speed) * var(--uib-d7)); }

        .matrix-loader .dot:nth-child(3) { --uib-scale: 0.98; }
        .matrix-loader .dot:nth-child(2), .matrix-loader .dot:nth-child(8) { --uib-scale: 0.96; }
        .matrix-loader .dot:nth-child(1), .matrix-loader .dot:nth-child(7) { --uib-scale: 0.94; }
        .matrix-loader .dot:nth-child(6), .matrix-loader .dot:nth-child(12) { --uib-scale: 0.92; }
        .matrix-loader .dot:nth-child(5), .matrix-loader .dot:nth-child(11) { --uib-scale: 0.9; }
        .matrix-loader .dot:nth-child(10), .matrix-loader .dot:nth-child(16) { --uib-scale: 0.88; }
        .matrix-loader .dot:nth-child(9), .matrix-loader .dot:nth-child(15) { --uib-scale: 0.86; }
        .matrix-loader .dot:nth-child(14) { --uib-scale: 0.84; }
        .matrix-loader .dot:nth-child(13) { --uib-scale: 0.82; }
    </style>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold text-dark mb-0">
                {{ __('Profile Settings') }}
            </h2>
            
            <div class="matrix-loader">
                <div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>
                <div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>
                <div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>
                <div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>
            </div>
        </div>
    </x-slot>

    <div class="container py-5">
        
        <div class="row g-4">
            
            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 animate-card" style="animation-delay: 0.1s;">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 animate-card" style="animation-delay: 0.2s;">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
            
        </div>

        {{-- <div class="row mt-2">
            <div class="col-12">
                <div class="card border border-danger border-opacity-25 bg-danger bg-opacity-10 shadow-sm rounded-4 animate-card" style="animation-delay: 0.3s;">
                    <div class="card-body p-4 p-md-5">
                        <div class="mx-auto" style="max-width: 600px;">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</x-app-layout>
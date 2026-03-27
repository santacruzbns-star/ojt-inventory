<x-app-layout>
    <style>
        /* Entrance Animation */
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

        /* Smooth Hover Lift Effect */
        .animate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; /* Bootstrap shadow-lg equivalent */
        }
    </style>

    <x-slot name="header">
        <h2 class="h4 fw-bold text-dark mb-0">
            {{ __('Profile Settings') }}
        </h2>
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
            </div> --}}
        </div>

    </div>
</x-app-layout>
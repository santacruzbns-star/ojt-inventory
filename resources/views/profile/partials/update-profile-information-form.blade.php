<section>
    <style>
        /* Reusing the wrapper styling for Name and Email */
        .profile-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .profile-input-wrapper .icon-left {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .profile-input-wrapper .form-control {
            padding-left: 45px !important;
        }

        .profile-input-wrapper:focus-within .icon-left {
            color: #1e6091; /* Accent Blue */
        }

        .btn-save-animated {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
            transition: all 0.3s ease;
        }
    </style>

    <header class="mb-4">
        <h2 class="h5 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-id-card text-primary"></i>
            {{ __('Profile Information') }}
        </h2>
        <p class="text-muted small mb-0">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" id="update-profile-form">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label fw-medium text-secondary small mb-1">
                {{ __('Name') }}
            </label>
            <div class="profile-input-wrapper">
                <i class="fa-solid fa-user icon-left"></i>
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="form-control w-100 py-2 @if($errors->has('name')) is-invalid @endif" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
            </div>
            @if($errors->has('name'))
                <div class="text-danger small mt-1 fw-medium">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <div class="mb-4">
            <label for="email" class="form-label fw-medium text-secondary small mb-1">
                {{ __('Email') }}
            </label>
            <div class="profile-input-wrapper">
                <i class="fa-solid fa-envelope icon-left"></i>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="form-control w-100 py-2 @if($errors->has('email')) is-invalid @endif" 
                    value="{{ old('email', $user->email) }}" 
                    required 
                    autocomplete="username" 
                />
            </div>
            @if($errors->has('email'))
                <div class="text-danger small mt-1 fw-medium">
                    {{ $errors->first('email') }}
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
                    <p class="text-dark small mb-2 fw-medium">
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification" class="btn btn-link p-0 m-0 text-decoration-none fw-bold small text-primary">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success small fw-medium mb-0">
                            <i class="fa-solid fa-paper-plane me-1"></i>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" id="save-profile-btn" class="btn btn-dark py-2 px-4 rounded-3 shadow-sm btn-save-animated">
                <span class="btn-text">{{ __('Save') }}</span>
                <l-ring 
                    id="profile-loader"
                    size="20"
                    stroke="3"
                    bg-opacity="0"
                    speed="2"
                    color="white"
                    style="display: none;"
                ></l-ring>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-success small fw-medium mb-0 ms-3 d-flex align-items-center gap-1"
                >
                    <i class="fa-solid fa-circle-check"></i> {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // UIBall LDRS Submit Animation Logic for Profile Form
            const form = document.getElementById('update-profile-form');
            const btn = document.getElementById('save-profile-btn');
            const btnText = btn.querySelector('.btn-text');
            const loader = document.getElementById('profile-loader');

            form.addEventListener('submit', function (e) {
                // Prevent double clicking
                if (btn.disabled) return;

                // Disable button and show LDRS loader
                btn.disabled = true;
                btn.style.opacity = '0.8';
                btn.style.cursor = 'not-allowed';
                
                btnText.textContent = 'Saving...';
                loader.style.display = 'inline-flex'; 
            });
        });
    </script>
</section>
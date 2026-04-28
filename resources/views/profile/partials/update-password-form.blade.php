<section>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/ring.js"></script>

    <style>
        /* Custom Input Wrapper Styling */
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-input-wrapper .icon-left {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .password-input-wrapper .icon-right {
            position: absolute;
            right: 16px;
            color: #94a3b8;
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 5px; /* Larger click target */
        }

        .password-input-wrapper .icon-right:hover {
            color: #1e6091; /* Goldtown Accent Blue */
        }

        /* Pad the inputs so text doesn't overlap the icons */
        .password-input-wrapper .form-control {
            padding-left: 45px !important;
            padding-right: 45px !important;
        }

        /* Focus state icon color change */
        .password-input-wrapper:focus-within .icon-left {
            color: #1e6091;
        }

        /* Submit Button Transitions */
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
            <i class="fa-solid fa-user-lock text-primary"></i>
            {{ __('Update Password') }}
        </h2>
        <p class="text-muted small mb-0">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" id="update-password-form">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-medium text-secondary small mb-1">
                {{ __('Current Password') }}
            </label>
            <div class="password-input-wrapper">
                <i class="fa-solid fa-unlock-keyhole icon-left"></i>
                <input 
                    id="update_password_current_password" 
                    name="current_password" 
                    type="password" 
                    class="form-control w-100 py-2 @if($errors->updatePassword->has('current_password')) is-invalid @endif" 
                    autocomplete="current-password" 
                />
                <i class="fa-solid fa-eye-slash icon-right toggle-password" data-target="update_password_current_password" title="Toggle Visibility"></i>
            </div>
            @if($errors->updatePassword->has('current_password'))
                <div class="text-danger small mt-1 fw-medium">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-medium text-secondary small mb-1">
                {{ __('New Password') }}
            </label>
            <div class="password-input-wrapper">
                <i class="fa-solid fa-key icon-left"></i>
                <input 
                    id="update_password_password" 
                    name="password" 
                    type="password" 
                    class="form-control w-100 py-2 @if($errors->updatePassword->has('password')) is-invalid @endif" 
                    autocomplete="new-password" 
                />
                <i class="fa-solid fa-eye-slash icon-right toggle-password" data-target="update_password_password" title="Toggle Visibility"></i>
            </div>
            @if($errors->updatePassword->has('password'))
                <div class="text-danger small mt-1 fw-medium">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label fw-medium text-secondary small mb-1">
                {{ __('Confirm Password') }}
            </label>
            <div class="password-input-wrapper">
                <i class="fa-solid fa-shield-check icon-left"></i>
                <input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="form-control w-100 py-2 @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" 
                    autocomplete="new-password" 
                />
                <i class="fa-solid fa-eye-slash icon-right toggle-password" data-target="update_password_password_confirmation" title="Toggle Visibility"></i>
            </div>
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="text-danger small mt-1 fw-medium">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" id="save-password-btn" class="btn btn-dark py-2 px-4 rounded-3 shadow-sm btn-save-animated">
                <span class="btn-text">{{ __('Save') }}</span>
                <l-ring 
                    id="save-loader"
                    size="20"
                    stroke="3"
                    bg-opacity="0"
                    speed="2"
                    color="white"
                    style="display: none;"
                ></l-ring>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-success small fw-medium mb-0 ms-3 d-flex align-items-center gap-1"
                >
                    <i class="fa-solid fa-circle-check"></i> {{ __('Password Updated.') }}
                </p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Password Visibility Toggle Logic
            const toggleButtons = document.querySelectorAll('.toggle-password');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Find the target input using the data-target attribute
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                        this.style.color = '#1e6091'; // Highlight when showing
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                        this.style.color = ''; // Reset color
                    }
                });
            });

            // 2. UIBall LDRS Submit Animation Logic
            const form = document.getElementById('update-password-form');
            const btn = document.getElementById('save-password-btn');
            const btnText = btn.querySelector('.btn-text');
            const loader = document.getElementById('save-loader');

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
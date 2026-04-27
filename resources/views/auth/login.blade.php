<x-guest-layout>
    <style>
        /* Animation styles for the Welcome Back text */
        .welcome-label .letter {
            display: inline-block;
            opacity: 0; /* Starts hidden for the Anime.js fade-in */
        }
    </style>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="form-header">
        <h3 class="welcome-label">Welcome Back</h3>
        <p>Please enter your details to sign in.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">{{ __('Username') }}</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-user icon-left"></i>
                <input 
                    id="email" 
                    type="text" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="Enter your username"
                    required 
                    autofocus 
                    autocomplete="username"
                    class="@error('email') is-invalid @enderror">
            </div>
            
            @error('email')
                <span class="input-error text-danger server-error">{{ $message }}</span>
            @enderror
            
            <span class="input-error text-danger client-error" id="email-empty-error" style="display: none;">
                Please enter your username.
            </span>
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock icon-left"></i>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required 
                    autocomplete="current-password"
                    class="@error('password') is-invalid @enderror">
            </div>
            
            @error('password')
                <span class="input-error text-danger server-error">{{ $message }}</span>
            @enderror

            <span class="input-error text-danger client-error" id="password-empty-error" style="display: none;">
                Please enter your password.
            </span>
        </div>

        <div class="form-options">
            <label class="checkbox-wrapper" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button type="submit" id="submit-btn">
            {{ __('Log in') }}
        </button>
    </form>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check Username
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-empty-error');
            if (!emailInput.value.trim()) {
                emailInput.classList.add('is-invalid');
                emailError.style.display = 'block';
                isValid = false;
            }

            // Check Password
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-empty-error');
            if (!passwordInput.value.trim()) {
                passwordInput.classList.add('is-invalid');
                passwordError.style.display = 'block';
                isValid = false;
            }

            // Stop form from submitting to server if fields are empty
            if (!isValid) {
                e.preventDefault(); 
            } else {
                // If valid, trigger the loading state
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right: 8px;"></i> Logging in...';
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.disabled = true; // Prevents double submission
            }
        });

        // Hide errors as soon as the user starts typing again
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                // Remove the red border
                this.classList.remove('is-invalid');
                
                // Hide client-side empty error
                const clientError = document.getElementById(this.id + '-empty-error');
                if (clientError) clientError.style.display = 'none';
                
                // Hide Laravel server error
                const serverError = this.closest('.form-group').querySelector('.server-error');
                if (serverError) serverError.style.display = 'none';
                
                // Reset button if they start typing after an error
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.innerHTML = '{{ __('Log in') }}';
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                submitBtn.disabled = false;
            });
        });
    </script>

    <script type="module">
        // Using the ESM build from CDN so native imports work directly in the browser
        import { animate, stagger } from 'https://cdn.jsdelivr.net/npm/animejs@3.2.2/+esm';

        document.addEventListener('DOMContentLoaded', () => {
            const welcomeLabel = document.querySelector('.welcome-label');
            
            if (welcomeLabel) {
                // Wrap every non-whitespace character in a <span> so they can move individually
                welcomeLabel.innerHTML = welcomeLabel.textContent.replace(/\S/g, "<span class='letter'>$&</span>");

                // Execute the wave animation
                animate('.welcome-label .letter', {
                    translateY: [20, 0],     // Slide up from 20px below
                    opacity: [0, 1],         // Fade from invisible to fully visible
                    duration: 800,           // 0.8 seconds per letter
                    ease: 'outExpo',         // Smooth snap into place
                    delay: stagger(50, { start: 600 }) // Wait 600ms for the card to slide up first, then animate each letter 50ms apart
                });
            }
        });
    </script>
</x-guest-layout>
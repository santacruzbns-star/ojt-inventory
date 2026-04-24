<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="form-header">
        <h3>Welcome Back</h3>
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
</x-guest-layout>
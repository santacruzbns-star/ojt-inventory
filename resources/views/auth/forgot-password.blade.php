<x-guest-layout>
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <div class="form-header">
        <h3>{{ __('Reset Password') }}</h3>
        <p style="line-height: 1.5;">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" id="reset-password-form" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-envelope icon-left"></i>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="name@example.com"
                    required 
                    autofocus
                    class="@error('email') is-invalid @enderror">
            </div>
            
            @error('email')
                <span class="input-error text-danger server-error">{{ $message }}</span>
            @enderror

            <span class="input-error text-danger client-error" id="email-empty-error" style="display: none;">
                Please enter your email address.
            </span>
        </div>

        <button type="submit" id="submit-btn" style="margin-top: 24px;">
            {{ __('Email Password Reset Link') }}
        </button>

        <div class="form-options" style="justify-content: center; margin-top: 16px;">
            @if (Route::has('login'))
                <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to Login</a>
            @endif
        </div>
    </form>

    <script>
        document.getElementById('reset-password-form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check Email
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-empty-error');
            
            if (!emailInput.value.trim()) {
                emailInput.classList.add('is-invalid');
                emailError.style.display = 'block';
                isValid = false;
            }

            // Stop form from submitting to server if the field is empty
            if (!isValid) {
                e.preventDefault(); 
            } else {
                // If valid, trigger the loading state
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right: 8px;"></i> Sending...';
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
                submitBtn.innerHTML = '{{ __('Email Password Reset Link') }}';
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                submitBtn.disabled = false;
            });
        });
    </script>
</x-guest-layout>
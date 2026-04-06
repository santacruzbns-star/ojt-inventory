<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="form-header">
        <h3>{{ __('Reset Password') }}</h3>
        <p style="line-height: 1.5;">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
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
                    autofocus>
            </div>
            @error('email')
                <span class="input-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" style="margin-top: 24px;">
            {{ __('Email Password Reset Link') }}
        </button>

        <div class="form-options" style="justify-content: center; margin-top: 16px;">
            @if (Route::has('login'))
                <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to Login</a>
            @endif
        </div>
    </form>
</x-guest-layout>
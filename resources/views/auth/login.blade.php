<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="form-header">
        <h3>Welcome Back</h3>
        <p>Please enter your details to sign in.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
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
                    autocomplete="username">
            </div>
            @error('email')
                <span class="input-error">{{ $message }}</span>
            @enderror
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
                    autocomplete="current-password">
            </div>
            @error('password')
                <span class="input-error">{{ $message }}</span>
            @enderror
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

        <button type="submit">
            {{ __('Log in') }}
        </button>
    </form>
</x-guest-layout>
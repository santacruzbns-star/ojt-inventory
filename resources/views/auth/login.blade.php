<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
    <x-input-label for="email" :value="__('Username')" />

    <div style="position: relative;">

                <!-- Icon -->
                <i class="fa fa-user"
                style="position:absolute; left:20px; top:50%; transform:translateY(-50%); color:rgb(180, 180, 180);">
                </i>

                <!-- Input -->
                <x-text-input id="email"
                    class="block mt-1 w-full"
                    style="padding-left:44px; font-size: 15px; "
                    type="text"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username" />

            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div style="position: relative;">
                

                <i class="fa fa-lock" 
                style="position:absolute; left:20px; top:50%; transform:translateY(-50%); color:rgb(180, 180, 180)">
                </i>

                <x-text-input id="password"
                    class="block mt-1 w-full"
                    style="padding-left:44px; font-size: 15px; font-weight:bold;"
                    type="password"
                    name="password"
                    required autocomplete="current-password" />

            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
    

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 d-flex align-items-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
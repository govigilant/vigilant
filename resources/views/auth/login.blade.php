<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                    autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="mt-4 flex justify-between items-center">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-white">{{ __('Remember me') }}</span>
                </label>

                <x-form.submit-button class="ms-4" submitText="Log in" />
            </div>

            <div class="flex justify-end items-center mt-4 gap-1">
                @if (Route::has('password.request'))
                    <a class="hover:underline text-sm text-white hover:text-red rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <a class="hover:underline text-sm text-white hover:text-red rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red"
                    href="{{ route('register') }}">
                    {{ __('Or create an account') }}
                </a>
            </div>

            @if (config('services.google.enabled'))
                <div class="mt-2">
                    <hr />
                    <div class="mt-4">
                        <a href="{{ route('login.socialite', ['provider' => 'google']) }}"
                            class="flex items-center justify-center gap-2 w-full py-2 px-4 rounded-md bg-blue text-white hover:bg-blue-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                            @svg('tni-google-o', 'size-6')
                            <span>Sign in with Google</span>
                        </a>
                    </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>

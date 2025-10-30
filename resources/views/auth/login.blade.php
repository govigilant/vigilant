<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-6" />

        @if (session('status'))
            <div class="mb-6 font-medium text-sm text-green-light bg-green/10 border border-green/30 rounded-lg px-4 py-3">
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

            <div class="mt-6 flex justify-between items-center">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-base-200">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift px-6 py-2.5 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-xl hover:shadow-red/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-300">
                    {{ __('Log in') }}
                </button>
            </div>

            <div class="flex justify-center items-center mt-6 gap-3 text-sm">
                @if (Route::has('password.request'))
                    <a class="text-base-300 hover:text-red transition-colors duration-200 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 rounded-md"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                    <span class="text-base-700">|</span>
                @endif

                <a class="text-base-300 hover:text-red transition-colors duration-200 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 rounded-md"
                    href="{{ route('register') }}">
                    {{ __('Create account') }}
                </a>
            </div>

            @if (config('services.google.enabled'))
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-base-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-base-900 text-base-400">Or continue with</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('login.socialite', ['provider' => 'google']) }}"
                            class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-lg bg-base-850 border border-base-700 text-base-100 hover:bg-base-800 hover:border-base-600 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 transition-all duration-200">
                            @svg('tni-google-o', 'size-6')
                            <span>Sign in with Google</span>
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>

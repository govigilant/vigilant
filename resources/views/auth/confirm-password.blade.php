<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6 text-sm text-base-300 leading-relaxed">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-6" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift px-6 py-2.5 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-xl hover:shadow-red/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-300">
                    {{ __('Confirm') }}
                </button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

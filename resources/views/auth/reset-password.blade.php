<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-6" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift px-6 py-2.5 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-xl hover:shadow-red/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-300">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false }">
            <div class="mb-6 text-sm text-base-300 leading-relaxed" x-show="! recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </div>

            <div class="mb-6 text-sm text-base-300 leading-relaxed" x-cloak x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </div>

            <x-validation-errors class="mb-6" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mt-4" x-show="! recovery">
                    <x-label for="code" value="{{ __('Code') }}" />
                    <x-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                </div>

                <div class="mt-4" x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="{{ __('Recovery Code') }}" />
                    <x-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mt-6">
                    <button type="button" class="text-sm text-base-300 hover:text-red transition-colors duration-200 cursor-pointer order-2 sm:order-1"
                                    x-show="! recovery"
                                    x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    ">
                        {{ __('Use a recovery code') }}
                    </button>

                    <button type="button" class="text-sm text-base-300 hover:text-red transition-colors duration-200 cursor-pointer order-2 sm:order-1"
                                    x-cloak
                                    x-show="recovery"
                                    x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    ">
                        {{ __('Use an authentication code') }}
                    </button>

                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift px-6 py-2.5 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-xl hover:shadow-red/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-300 order-1 sm:order-2">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>

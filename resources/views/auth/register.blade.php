<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <!-- Welcome header with fade-in animation -->
        <div class="mb-8 text-center opacity-0 translate-y-4 animate-[fadeInUp_0.5s_ease-out_0.05s_forwards]">
            <h2 class="text-2xl font-semibold bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">
                {{ __('Create your account') }}
            </h2>
            <p class="mt-2 text-sm text-base-400">{{ __('Join us and start monitoring today') }}</p>
        </div>

        <x-validation-errors class="mb-6" />

        <form method="POST" action="{{ route('register') }}" x-data="{
            passwordStrength: 0,
            showPasswordStrength: false,
            step: 0,
            checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                if (password.match(/\d/)) strength++;
                if (password.match(/[^a-zA-Z\d]/)) strength++;
                this.passwordStrength = strength;
            }
        }" x-init="setTimeout(() => step = 1, 80)">
            @csrf

            <!-- Name field - Staggered fade in -->
            <div class="opacity-0 translate-y-4 transition-all duration-400"
                :class="step >= 1 && 'opacity-100 translate-y-0'"
                x-init="setTimeout(() => step = 2, 160)">
                <div class="relative group">
                    <x-label for="name" value="{{ __('Name') }}" class="transition-colors duration-150 group-focus-within:text-red" />
                    <div class="relative mt-1">
                        <x-input id="name" class="block w-full transition-all duration-200 hover:ring-2 hover:ring-red/20 focus-within:scale-[1.01]" 
                            type="text" name="name" :value="old('name')" required
                            autofocus autocomplete="name" />
                        <!-- Animated check icon when field has value -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 scale-0 transition-all duration-200"
                            x-data="{ show: false }"
                            x-init="$el.previousElementSibling.addEventListener('input', (e) => show = e.target.value.length > 0)"
                            :class="show && 'opacity-100 scale-100'">
                            <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email field - Staggered fade in -->
            <div class="mt-6 opacity-0 translate-y-4 transition-all duration-400"
                :class="step >= 2 && 'opacity-100 translate-y-0'"
                x-init="setTimeout(() => step = 3, 320)">
                <div class="relative group">
                    <x-label for="email" value="{{ __('Email') }}" class="transition-colors duration-150 group-focus-within:text-red" />
                    <div class="relative mt-1">
                        <x-input id="email" class="block w-full transition-all duration-200 hover:ring-2 hover:ring-red/20 focus-within:scale-[1.01]" 
                            type="email" name="email" :value="old('email')"
                            required autocomplete="username" />
                        <!-- Animated check icon when valid email -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 scale-0 transition-all duration-200"
                            x-data="{ show: false }"
                            x-init="$el.previousElementSibling.addEventListener('input', (e) => show = e.target.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/))"
                            :class="show && 'opacity-100 scale-100'">
                            <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password field with strength indicator - Staggered fade in -->
            <div class="mt-6 opacity-0 translate-y-4 transition-all duration-400"
                :class="step >= 3 && 'opacity-100 translate-y-0'"
                x-init="setTimeout(() => step = 4, 480)">
                <div class="relative group">
                    <x-label for="password" value="{{ __('Password') }}" class="transition-colors duration-150 group-focus-within:text-red" />
                    <div class="relative mt-1">
                        <x-input id="password" class="block w-full transition-all duration-200 hover:ring-2 hover:ring-red/20 focus-within:scale-[1.01]" 
                            type="password" name="password" required
                            autocomplete="new-password"
                            @focus="showPasswordStrength = true"
                            @input="checkPasswordStrength($event.target.value)" />
                    </div>
                    
                    <!-- Password strength indicator with smooth transitions -->
                    <div class="mt-3 space-y-2 overflow-hidden transition-all duration-400"
                        x-show="showPasswordStrength"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="flex gap-1.5">
                            <div class="h-1.5 flex-1 rounded-full bg-base-800 overflow-hidden transition-all duration-400"
                                :class="passwordStrength >= 1 && 'bg-gradient-to-r from-red to-orange'"></div>
                            <div class="h-1.5 flex-1 rounded-full bg-base-800 overflow-hidden transition-all duration-400 delay-[60ms]"
                                :class="passwordStrength >= 2 && 'bg-gradient-to-r from-orange to-yellow'"></div>
                            <div class="h-1.5 flex-1 rounded-full bg-base-800 overflow-hidden transition-all duration-400 delay-[80ms]"
                                :class="passwordStrength >= 3 && 'bg-gradient-to-r from-yellow to-green-light'"></div>
                            <div class="h-1.5 flex-1 rounded-full bg-base-800 overflow-hidden transition-all duration-400 delay-[120ms]"
                                :class="passwordStrength >= 4 && 'bg-gradient-to-r from-green to-green-light'"></div>
                        </div>
                        <p class="text-xs transition-all duration-200"
                            :class="{
                                'text-red': passwordStrength <= 1,
                                'text-orange': passwordStrength === 2,
                                'text-yellow': passwordStrength === 3,
                                'text-green-light': passwordStrength === 4
                            }">
                            <span x-show="passwordStrength === 0">{{ __('Enter a password') }}</span>
                            <span x-show="passwordStrength === 1">{{ __('Weak password') }}</span>
                            <span x-show="passwordStrength === 2">{{ __('Fair password') }}</span>
                            <span x-show="passwordStrength === 3">{{ __('Good password') }}</span>
                            <span x-show="passwordStrength === 4">{{ __('Strong password! 🎉') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Password confirmation field - Staggered fade in -->
            <div class="mt-6 opacity-0 translate-y-4 transition-all duration-400"
                :class="step >= 4 && 'opacity-100 translate-y-0'"
                x-init="setTimeout(() => step = 5, 640)">
                <div class="relative group">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="transition-colors duration-150 group-focus-within:text-red" />
                    <div class="relative mt-1">
                        <x-input id="password_confirmation" class="block w-full transition-all duration-200 hover:ring-2 hover:ring-red/20 focus-within:scale-[1.01]" 
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                        <!-- Animated check icon when passwords match -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 scale-0 transition-all duration-200"
                            x-data="{ show: false }"
                            x-init="$el.previousElementSibling.addEventListener('input', (e) => {
                                const pwd = document.getElementById('password').value;
                                show = e.target.value.length > 0 && e.target.value === pwd;
                            })"
                            :class="show && 'opacity-100 scale-100'">
                            <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms checkbox - Staggered fade in -->
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-6 opacity-0 translate-y-4 transition-all duration-400"
                    :class="step >= 5 && 'opacity-100 translate-y-0'">
                    <div class="relative group">
                        <x-label for="terms">
                            <div class="flex items-start p-4 rounded-lg border border-base-700 bg-base-850/50 transition-all duration-200 hover:border-base-600 hover:bg-base-850">
                                <x-checkbox name="terms" id="terms" required class="mt-0.5 transition-transform duration-150 hover:scale-110" />

                                <div class="ms-3 text-sm text-base-300">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' =>
                                            '<a target="_blank" href="' .
                                            route('terms.show') .
                                            '" class="text-base-200 hover:text-red transition-colors duration-150 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 inline-flex items-center gap-1 group/link">' .
                                            __('Terms of Service') .
                                            '<svg class="w-3 h-3 opacity-0 -translate-x-1 transition-all duration-150 group-hover/link:opacity-100 group-hover/link:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>' .
                                            '</a>',
                                        'privacy_policy' =>
                                            '<a target="_blank" href="' .
                                            route('policy.show') .
                                            '" class="text-base-200 hover:text-red transition-colors duration-150 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 inline-flex items-center gap-1 group/link">' .
                                            __('Privacy Policy') .
                                            '<svg class="w-3 h-3 opacity-0 -translate-x-1 transition-all duration-150 group-hover/link:opacity-100 group-hover/link:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>' .
                                            '</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                </div>
            @else
                <!-- If no terms, show button earlier -->
                <div x-init="setTimeout(() => step = 5, 640)" class="hidden"></div>
            @endif

            <!-- Submit button with pulsing attention grabber - Staggered fade in -->
            <div class="mt-8 opacity-0 translate-y-4 transition-all duration-400"
                :class="step >= 5 && 'opacity-100 translate-y-0'"
                x-init="setTimeout(() => step = 6, 960)">
                <button type="submit"
                    class="group relative w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift px-6 py-3 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-2xl hover:shadow-red/40 hover:scale-[1.02] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-250 overflow-hidden">
                    <!-- Shimmer effect on hover -->
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-800 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    <span class="relative">{{ __('Create Account') }}</span>
                    <svg class="relative w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>

            <!-- Already registered link - Staggered fade in -->
            <div class="flex justify-center items-center mt-6 text-sm opacity-0 transition-all duration-400"
                :class="step >= 6 && 'opacity-100'">
                <a class="text-base-300 hover:text-red transition-all duration-150 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 inline-flex items-center gap-2 group/login"
                    href="{{ route('login') }}">
                    <svg class="w-4 h-4 transition-transform duration-150 group-hover/login:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Already registered? Sign in') }}
                </a>
            </div>

            @if (config('services.google.enabled'))
                <div class="mt-8 opacity-0 transition-all duration-400"
                    :class="step >= 6 && 'opacity-100'">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-base-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-base-900 text-base-400">Or continue with</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('login.socialite', ['provider' => 'google']) }}"
                            class="group flex items-center justify-center gap-3 w-full py-2.5 px-4 rounded-lg bg-base-850 border border-base-700 text-base-100 hover:bg-base-800 hover:border-base-600 hover:scale-[1.01] focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 transition-all duration-250">
                            <span class="transition-transform duration-250 group-hover:rotate-[360deg]">
                                @svg('tni-google-o', 'size-6')
                            </span>
                            <span>Sign in with Google</span>
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>

<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                    autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' =>
                                        '<a target="_blank" href="' .
                                        route('terms.show') .
                                        '" class="underline text-sm text-white hover:text-red rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red">' .
                                        __('Terms of Service') .
                                        '</a>',
                                    'privacy_policy' =>
                                        '<a target="_blank" href="' .
                                        route('policy.show') .
                                        '" class="underline text-sm text-white hover:text-red rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red">' .
                                        __('Privacy Policy') .
                                        '</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-white hover:text-red rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red"
                    href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-form.submit-button submitText="Register" class="ms-4" />
            </div>

            @if (config('services.google.enabled'))
                <div class="mt-2">
                    <hr />
                    <div class="mt-4">
                        <a href="{{ route('login.socialite', ['provider' => 'google']) }}"
                            class="flex items-center justify-center gap-2 w-full py-2 px-4 rounded-md bg-blue text-white hover:bg-blue-light focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                            @svg('tni-google-o', 'size-6')
                            <span>Sign in with Google</span>
                        </a>
                    </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>

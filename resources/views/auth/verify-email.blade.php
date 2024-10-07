<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-white">
            {{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-form.submit-button submitText="Resend Verification Email" type="submit"/>
                </div>
            </form>

            <div>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    {{ csrf_field() }}

                    <x-form.submit-button submitText="Log Out"/>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>

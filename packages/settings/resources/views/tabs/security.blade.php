<div class="max-w-7xl mx-auto">
    <form wire:submit="updatePassword">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.header>@lang('Password')</x-form.header>

            <x-form.password class="sm:col-span-2"
                             field="password.current_password"
                             name="Current Password"
                             live="false"
            />

            <x-form.password class="sm:col-span-2"
                             field="password.password"
                             name="New Password"
                             live="false"
            />

            <x-form.password class="sm:col-span-2"
                             field="password.password_confirmation"
                             name="New Password Confirmation"
                             live="false"
            />

            <x-form.submit-button submitText="Change"/>
        </div>
    </form>

    <x-form.header>@lang('Additional Security Settings')</x-form.header>

    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="mt-6">
            @livewire('profile.two-factor-authentication-form')
        </div>
    @endif

    <div class="mt-6">
        @livewire('profile.logout-other-browser-sessions-form')
    </div>

</div>

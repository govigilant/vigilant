<div class="max-w-7xl mx-auto">

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.header>@lang('Profile')</x-form.header>

            <x-form.text class="sm:col-span-2"
                         field="form.name"
                         name="Name"
                         required
                         autocomplete="name"
            />

            <x-form.text class="sm:col-span-2"
                         field="form.email"
                         name="E-Mail"
                         description="Your account's E-mail address"
                         required
                         autocomplete="email"
            />

            <x-form.submit-button submitText="Save"/>
        </div>
    </form>


    {{--    Password change--}}

    {{--     Delete account --}}



    {{--    @if (Laravel\Fortify\Features::canUpdateProfileInformation())--}}
    {{--        @livewire('profile.update-profile-information-form')--}}

    {{--        <x-section-border/>--}}
    {{--    @endif--}}

    {{--    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))--}}
    {{--        <div class="mt-10 sm:mt-0">--}}
    {{--            @livewire('profile.update-password-form')--}}
    {{--        </div>--}}

    {{--        <x-section-border/>--}}
    {{--    @endif--}}

    {{--    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())--}}
    {{--        <x-section-border/>--}}

    {{--        <div class="mt-10 sm:mt-0">--}}
    {{--            @livewire('profile.delete-user-form')--}}
    {{--        </div>--}}
    {{--    @endif--}}
</div>

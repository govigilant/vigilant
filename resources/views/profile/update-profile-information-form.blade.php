<div>
    <form wire:submit="updateProfileInformation">

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                       wire:model.live="photo"
                       x-ref="photo"
                       x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            "/>

                <x-label for="photo" value="{{ __('Photo') }}"/>

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                         class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2"/>
            </div>
        @endif

        <x-form.text class="sm:col-span-2"
                     field="state.name"
                     name="Name"
                     description="Name"
                     required
                     autocomplete="name"
                     placeholder="{{ config('app.url') }}"/>

        {{--    <div class="col-span-6 sm:col-span-4">--}}
        {{--        <x-label for="name" value="{{ __('Name') }}"/>--}}
        {{--        <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name"/>--}}
        {{--        <x-input-error for="name" class="mt-2"/>--}}
        {{--    </div>--}}

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}"/>
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required
                     autocomplete="username"/>
            <x-input-error for="email" class="mt-2"/>

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Your email address is unverified.') }}

                    <button type="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red"
                            wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <x-action-message class="me-3 text-green-light" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-form.submit-button class="bg-red" wire:loading.attr="disabled" wire:target="photo" submitText="Save"/>
    </form>

</div>

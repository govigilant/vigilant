<x-form-section submit="updateTeamName">
    <x-slot name="title">
        {{ __('Team Name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('The team\'s name and owner information.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            <x-label value="{{ __('Team Owner') }}"/>

            <div class="flex items-center mt-2">
                <div class="leading-tight">
                    <div class="text-base-100">{{ $team->owner->name }}</div>
                    <div class="text-white text-sm">{{ $team->owner->email }}</div>
                </div>
            </div>
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Team Name') }}"/>

            <x-input id="name"
                     type="text"
                     class="mt-1 block w-full"
                     wire:model="state.name"
                     :disabled="! Gate::check('update', $team)"/>

            <x-label class="mt-2" for="state.timezone" value="{{ __('Timezone') }}"/>

            <x-form.select field="state.timezone"
                           :inline="true"
            >
                @foreach(DateTimeZone::listIdentifiers() as $timezone)
                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                @endforeach
            </x-form.select>

            <x-input-error for="name" class="mt-2"/>
        </div>
    </x-slot>

    @if (Gate::check('update', $team))
        <x-slot name="actions">
            <x-action-message class="me-3" on="saved">
                {{ __('Saved.') }}
            </x-action-message>

            <x-form.button class="bg-red">
                {{ __('Save') }}
            </x-form.button>
        </x-slot>
    @endif
</x-form-section>

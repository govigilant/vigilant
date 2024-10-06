<x-action-section>
    <x-slot name="title">
        {{ __('Delete Team') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete this team.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-base-100">
            {{ __('Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain.') }}
            <br/>
            <span class="text-red text-md">{{ __('This action is non-reverable, all data will be lost.') }}</span>
        </div>

        <div class="mt-5">

            <x-danger-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
                {{ __('Delete Team') }}
            </x-danger-button>
        </div>

        <x-confirmation-modal wire:model.live="confirmingTeamDeletion">
            <x-slot name="title">
                {{ __('Delete Team') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted.') }}
            </x-slot>

            <x-slot name="footer">
                <x-button wire:click="$toggle('confirmingTeamDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-button>

                <x-danger-button class="ms-3" wire:click="deleteTeam" wire:loading.attr="disabled">
                    {{ __('Delete Team') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </x-slot>
</x-action-section>

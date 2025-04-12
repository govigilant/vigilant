<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('certificates')" :title="'Certificate Monitor - ' . $monitor->domain . ($monitor->enabled ? '' : ' (Disabled)')" x-data="{ submitForm() { if (confirm('Are you sure you want to delete this monitor?')) { $refs.form.submit(); } } }">
            <form action="{{ route('certificates.delete', ['monitor' => $monitor]) }}" method="POST" wire:ignore
                x-ref="form" onsubmit="return confirm('Are you sure you want to delete this monitor?');">
                @csrf
                @method('DELETE')
            </form>

            <x-frontend::page-header.actions>
                <x-form.button dusk="lighthouse-edit-button"
                    href="{{ route('certificates.edit', ['monitor' => $monitor]) }}" class="bg-blue hover:bg-blue-light">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button href="{{ route('certificates.edit', ['monitor' => $monitor]) }}"
                    class="bg-blue hover:bg-blue-light">
                    @lang('Edit')
                    </x-form.button>
                    <x-form.dropdown-button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                        @lang('Delete')
                        </x-form.button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:certificate-monitor-dashboard :monitorId="$monitor->id" />

    <div class="my-8">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('History') }}</h2>
        <p class="text-sm text-neutral-400 mb-4">
            @lang('View the history of this certificate monitor.')
        </p>
        <livewire:certificate-monitor-history-table :monitorId="$monitor->id" />
    </div>


</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('cve.index')" :title="'CVE Monitor - ' . $monitor->keyword . (!$monitor->enabled ? ' (Disabled)' : '')" x-data="{ submitForm() { if (confirm('Are you sure you want to delete this monitor?')) { $refs.form.submit(); } } }">
            <form action="{{ route('cve.monitor.delete', ['monitor' => $monitor]) }}" method="POST" x-ref="form">
                @csrf
                @method('DELETE')
            </form>
            <x-frontend::page-header.actions>
                <x-form.button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>
                <x-form.button dusk="monitor-edit-button" class="bg-blue hover:bg-blue-light" :href="route('cve.monitor.edit', ['monitor' => $monitor])">
                    @lang('Edit')
                </x-form.button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button :href="route('cve.monitor.edit', ['monitor' => $monitor])">
                    @lang('Edit')
                </x-form.dropdown-button>

                <x-form.dropdown-button x-on:click="submitForm">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <div>
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Matched CVE\'s') }}
        </h2>

        <livewire:cve-monitor-matches-table :monitor="$monitor" wire:key="matches-table" />
    </div>

</x-app-layout>

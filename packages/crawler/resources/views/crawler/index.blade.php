<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('crawler.index')" :title="'Crawler - ' . $crawler->start_url . ($crawler->enabled ? '' : ' (Disabled)')" x-data="{ submitForm() { if (confirm('Are you sure you want to delete this monitor?')) { $refs.form.submit(); } } }">
            <form action="{{ route('crawler.delete', ['crawler' => $crawler]) }}" method="POST" wire:ignore x-ref="form"
                onsubmit="return confirm('Are you sure you want to delete this monitor?');">
                @csrf
                @method('DELETE')
            </form>


            <x-frontend::page-header.actions>
                <x-form.button dusk="crawler-edit-button" class="bg-blue hover:bg-blue-light" :href="route('crawler.edit', ['crawler' => $crawler])">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>

            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button :href="route('crawler.edit', ['crawler' => $crawler])">
                    @lang('Edit')
                </x-form.dropdown-button>
                <x-form.dropdown-button x-on:click="submitForm">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:crawler-dashboard :crawlerId="$crawler->id" wire:key="crawher-dashboard" />

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Issues') }}</h2>

        <livewire:crawler-issues-table :crawlerId="$crawler->id" wire:key="issues-table" />
    </div>

</x-app-layout>

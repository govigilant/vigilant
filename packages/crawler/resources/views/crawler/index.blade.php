<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('crawler.index')" title="Crawler - {{ $crawler->start_url }}">
            <x-form.button dusk="crawler-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('crawler.edit', ['crawler' => $crawler])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:crawler-dashboard :crawlerId="$crawler->id"/>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Issues') }}</h2>

        <livewire:crawler-issues-table :crawlerId="$crawler->id" wire:key="issues-table"/>
    </div>

</x-app-layout>

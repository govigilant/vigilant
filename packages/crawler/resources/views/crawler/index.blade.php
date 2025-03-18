<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('crawler.index')" :title="'Crawler - ' . $crawler->start_url . ($crawler->enabled ? '' : ' (Disabled)')">
            <form action="{{ route('crawler.delete', ['crawler' => $crawler]) }}" method="POST" wire:ignore
                onsubmit="return confirm('Are you sure you want to delete this monitor?');">
                @csrf
                @method('DELETE')
                <x-form.button class="bg-red hover:bg-red-light" type="submit">
                    @lang('Delete')
                </x-form.button>
            </form>
            <x-form.button dusk="crawler-edit-button" class="bg-blue hover:bg-blue-light" :href="route('crawler.edit', ['crawler' => $crawler])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:crawler-dashboard :crawlerId="$crawler->id" wire:key="crawher-dashboard" />

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Issues') }}</h2>

        <livewire:crawler-issues-table :crawlerId="$crawler->id" wire:key="issues-table" />
    </div>

</x-app-layout>

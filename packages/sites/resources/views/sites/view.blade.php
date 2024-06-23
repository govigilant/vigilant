<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('sites')" title="Site - {{ $site->url }}">
            <x-form.button dusk="site-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('site.edit', ['site' => $site])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

</x-app-layout>

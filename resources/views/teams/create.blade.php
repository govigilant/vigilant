<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create Team"></x-page-header>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('teams.create-team-form')
        </div>
    </div>
</x-app-layout>

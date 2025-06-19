<div wire:init="checkStepFinished">
    <x-slot name="header">
        <x-page-header title="Let's get started" />
    </x-slot>

    <div class="max-w-7xl mx-auto mb-6">
        <p class="text-lg text-base-100 font-text">
            @lang(':name, thank you for signing up for Vigilant!', ['name' => $name])
            <br />
            @lang('Import your websites to quickly start monitoring.')
        </p>
    </div>

    <livewire:sites.import :inline="true" />

</div>

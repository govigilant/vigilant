<div wire:init="checkStepFinished">
    <x-slot name="header">
        <x-page-header title="Setup your notification channel" />
    </x-slot>

    <div class="max-w-7xl mx-auto mb-6">
        <p class="text-lg text-base-100 font-text">
            @lang('Next, setup where you want to receive notifications.')
        </p>
    </div>

    <livewire:channel-form :inline="true" />
</div>

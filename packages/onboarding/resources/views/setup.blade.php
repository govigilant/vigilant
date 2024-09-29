<div>
    <x-slot name="header">
        <x-page-header title="Get Started With Vigilant"/>
    </x-slot>

    <div class="max-w-7xl mx-auto mb-6">
        <p class="text-md text-base-100">
            @lang('Welcome to Vigilant! To get started monitoring your website enter it below.')
        </p>
    </div>

    <livewire:sites.create :inline="true" />

    <div class="max-w-7xl mx-auto mt-6 flex justify-end">
        <x-form.button class="bg-red hover:bg-red-light" wire:click="save">
            @lang('Add Site')
        </x-form.button>
    </div>
</div>

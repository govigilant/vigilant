<div wire:init="checkStepFinished" class="min-h-screen">
    <x-slot name="header">
        <x-page-header title="Setup Notifications" />
    </x-slot>

    <!-- Progress Steps -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="flex items-center justify-center gap-4">
            <!-- Step 1 -->
            <div class="flex items-center opacity-75">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green text-base-100 font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="ml-3 text-base-300 font-semibold">Add Sites</span>
            </div>
            
            <!-- Connector -->
            <div class="flex-1 h-1 bg-green mx-4"></div>
            
            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-red to-orange text-base-100 font-bold">
                    2
                </div>
                <span class="ml-3 text-base-100 font-semibold">Notifications</span>
            </div>
            
            <!-- Connector -->
            <div class="flex-1 h-1 bg-base-700 mx-4"></div>
            
            <!-- Step 3 -->
            <div class="flex items-center opacity-50">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-base-700 text-base-400 font-bold">
                    3
                </div>
                <span class="ml-3 text-base-400 font-semibold">Done</span>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="max-w-4xl mx-auto mb-8 text-center">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent mb-4">
            @lang('Setup Your Notification Channel')
        </h2>
        <p class="text-lg text-base-300 max-w-2xl mx-auto">
            @lang('Choose how you want to receive alerts when your sites have issues. You can add more channels later.')
        </p>
    </div>

    <!-- Channel Form -->
    <div class="max-w-4xl mx-auto">
        <livewire:channel-form :inline="true" />
        
        <!-- Navigation -->
        <div class="mt-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button wire:click="goBack" class="inline-flex items-center gap-2 text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    @lang('Back to add sites')
                </button>
                <button wire:click="skipOnboarding" class="text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
                    @lang('Skip onboarding entirely')
                </button>
            </div>
            <button wire:click="redirectNextStep" class="text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
                @lang('Skip this step →')
            </button>
        </div>
    </div>
</div>

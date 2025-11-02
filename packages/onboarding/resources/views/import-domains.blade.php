<div wire:init="checkStepFinished" class="min-h-screen">
    <x-slot name="header">
        <x-page-header title="Get Started with Vigilant" />
    </x-slot>

    <!-- Progress Steps -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="flex items-center justify-center gap-4">
            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-red to-orange text-base-100 font-bold">
                    1
                </div>
                <span class="ml-3 text-base-100 font-semibold">Add Sites</span>
            </div>
            
            <!-- Connector -->
            <div class="flex-1 h-1 bg-base-700 mx-4"></div>
            
            <!-- Step 2 -->
            <div class="flex items-center opacity-50">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-base-700 text-base-400 font-bold">
                    2
                </div>
                <span class="ml-3 text-base-400 font-semibold">Notifications</span>
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
        <h2 class="text-3xl font-bold mb-4">
            <span class="bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">@lang('Welcome to Vigilant, :name!', ['name' => $name])</span> 
            <span class="inline-block">👋</span>
        </h2>
        <p class="text-lg text-base-300 max-w-2xl mx-auto">
            @lang('Let\'s get you started by importing your websites. Add your domains below and we\'ll set up monitoring for you.')
        </p>
    </div>

    <!-- Import Form -->
    <div class="max-w-4xl mx-auto">
        <livewire:sites.import :inline="true" />
        
        <!-- Skip Option -->
        <div class="mt-6 flex items-center justify-between">
            <button wire:click="skipOnboarding" class="text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
                @lang('Skip onboarding entirely')
            </button>
            <button wire:click="redirectNextStep" class="text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
                @lang('Skip this step →')
            </button>
        </div>
    </div>

    <!-- Features Preview -->
    <div class="max-w-4xl mx-auto mt-16">
        <h3 class="text-xl font-bold text-base-100 mb-6 text-center">@lang('What you\'ll get')</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-base-850 border border-base-700 rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-lg bg-blue/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-base-100 mb-2">@lang('Uptime Monitoring')</h4>
                <p class="text-sm text-base-400">@lang('24/7 monitoring to ensure your sites stay online')</p>
            </div>
            
            <div class="bg-base-850 border border-base-700 rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-lg bg-green/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-base-100 mb-2">@lang('Performance Tracking')</h4>
                <p class="text-sm text-base-400">@lang('Monitor site speed and performance metrics')</p>
            </div>
            
            <div class="bg-base-850 border border-base-700 rounded-lg p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-lg bg-orange/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-base-100 mb-2">@lang('Instant Alerts')</h4>
                <p class="text-sm text-base-400">@lang('Get notified immediately when issues arise')</p>
            </div>
        </div>
    </div>
</div>

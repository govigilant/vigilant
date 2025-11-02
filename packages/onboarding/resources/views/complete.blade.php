<div wire:init="checkStepFinished" class="min-h-screen">
    <x-slot name="header">
        <x-page-header title="Get Started with Vigilant" />
    </x-slot>

    <!-- Progress Steps -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="flex items-center justify-center gap-4">
            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green text-base-100 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="ml-3 text-base-100 font-semibold">Add Sites</span>
            </div>

            <!-- Connector -->
            <div class="flex-1 h-1 bg-green mx-4"></div>

            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green text-base-100 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="ml-3 text-base-100 font-semibold">Notifications</span>
            </div>

            <!-- Connector -->
            <div class="flex-1 h-1 bg-gradient-to-r from-green to-orange mx-4"></div>

            <!-- Step 3 -->
            <div class="flex items-center">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-red to-orange text-base-100 font-bold">
                    3
                </div>
                <span class="ml-3 text-base-100 font-semibold">Done</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto mb-8 text-center">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green/20 flex items-center justify-center">
                <svg class="w-10 h-10 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-bold mb-4">
            <span
                class="bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">@lang('You\'re all set!')</span>
            <span class="inline-block">🎉</span>
        </h2>
        <p class="text-lg text-base-300 max-w-2xl mx-auto">
            @lang('Vigilant is now monitoring your websites. Here\'s what happens next.')
        </p>
    </div>

    <div class="max-w-4xl mx-auto mb-12">
        <div class="space-y-4">
            <a href="{{ route('sites') }}" wire:navigate.hover
                class="block bg-base-850 border border-base-700 rounded-lg p-6 transition-all duration-200 hover:border-blue/50 hover:shadow-lg hover:shadow-blue/10 hover:-translate-y-0.5 cursor-pointer group">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue/20 flex items-center justify-center group-hover:bg-blue/30 transition-colors duration-200">
                        <svg class="w-5 h-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-base-100 font-semibold mb-1 group-hover:text-blue transition-colors duration-200">
                            @lang('Initial Checks Running')</h3>
                        <p class="text-base-400 text-sm">
                            @lang('We\'re running the first checks on your websites right now. This usually takes a few hours. You\'ll see the results appear in your dashboard soon.')
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-base-600 group-hover:text-blue transition-all duration-200 group-hover:translate-x-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('notifications') }}" wire:navigate.hover
                class="block bg-base-850 border border-base-700 rounded-lg p-6 transition-all duration-200 hover:border-orange/50 hover:shadow-lg hover:shadow-orange/10 hover:-translate-y-0.5 cursor-pointer group">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-orange/20 flex items-center justify-center group-hover:bg-orange/30 transition-colors duration-200">
                        <svg class="w-5 h-5 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-base-100 font-semibold mb-1 group-hover:text-orange transition-colors duration-200">
                            @lang('Notifications Are Active')</h3>
                        <p class="text-base-400 text-sm">
                            @lang('You\'ll receive alerts when we detect issues with your websites. Make sure to check your notification channels are working correctly.')
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-base-600 group-hover:text-orange transition-all duration-200 group-hover:translate-x-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('cve.index') }}" wire:navigate.hover
                class="block bg-base-850 border border-base-700 rounded-lg p-6 transition-all duration-200 hover:border-red/50 hover:shadow-lg hover:shadow-red/10 hover:-translate-y-0.5 cursor-pointer group">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-red/20 flex items-center justify-center group-hover:bg-red/30 transition-colors duration-200">
                        <svg class="w-5 h-5 text-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-base-100 font-semibold mb-1 group-hover:text-red transition-colors duration-200">
                            @lang('Setup CVE Monitoring')</h3>
                        <p class="text-base-400 text-sm">
                            @lang('Get alerted about security vulnerabilities affecting your technology stack. Configure CVE monitors to track packages and technologies you use.')
                        </p>
                    </div>
                    <svg class="w-5 h-5 text-base-600 group-hover:text-red transition-all duration-200 group-hover:translate-x-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <button wire:click="goBack" class="text-base-400 hover:text-base-200 text-sm transition-colors duration-200">
            @lang('← Go back')
        </button>

        <a type="button" href="{{ route('sites') }}" wire:navigate.hover
            class="px-6 py-3 bg-gradient-to-r from-red to-orange text-base-100 font-semibold rounded-lg hover:shadow-lg hover:shadow-orange/20 transition-all duration-200">
            @lang('View My Sites →')
        </a>
    </div>
</div>

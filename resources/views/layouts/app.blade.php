<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '') - Vigilant</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-text antialiased dark dark:bg-base-black h-screen flex" x-data="{ sidebarOpen: false, contentShow: true }"
    @navigation-start.window="contentShow = false" @navigation-end.window="contentShow = true">
    <x-layout.sidebar />

    <main class="dark:bg-base-black flex flex-col flex-1 relative min-w-0">
        <!-- Diagonal light streaks -->
        <div class="absolute left-0 top-0 w-96 h-full pointer-events-none opacity-30">
            <div
                class="absolute top-1/4 -left-24 w-px h-32 bg-gradient-to-b from-transparent via-red to-transparent rotate-12 blur-sm">
            </div>
            <div class="absolute top-1/2 -left-32 w-px h-48 bg-gradient-to-b from-transparent via-orange to-transparent rotate-12 blur-sm"
                style="animation: pulse 3s ease-in-out infinite;"></div>
            <div class="absolute top-3/4 -left-20 w-px h-40 bg-gradient-to-b from-transparent via-red-light to-transparent rotate-12 blur-sm"
                style="animation: pulse 3s ease-in-out infinite 1s;"></div>
        </div>

        <!-- Floating orbs -->
        <div
            class="absolute left-4 top-16 w-2 h-2 rounded-full bg-red blur-sm pointer-events-none opacity-60 animate-float">
        </div>
        <div class="absolute left-8 top-48 w-1.5 h-1.5 rounded-full bg-orange blur-sm pointer-events-none opacity-40 animate-float"
            style="animation-delay: -3s;"></div>
        <div class="absolute left-2 top-96 w-2.5 h-2.5 rounded-full bg-red-light blur-sm pointer-events-none opacity-50 animate-float"
            style="animation-delay: -1.5s;"></div>

        <div class="bg-base-900 flex flex-col flex-1 relative overflow-y-auto min-w-0">
            @if (isset($header))
                <header
                    class="bg-gradient-to-r from-base-950 to-base-900 px-8 py-6 border-b border-base-800/50 relative z-10">
                    {{ $header }}
                </header>
            @endif

            <div class="px-4 sm:px-6 lg:px-8 pt-6 pb-6 w-full relative z-10 min-w-0"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                :style="contentShow ? '' : 'opacity: 0'">
                <div>
                    <x-alert />
                </div>
                <x-banner />
                {{ $slot }}
            </div>

            <!-- Loading indicator -->
            <div x-show="!contentShow" x-transition:enter="transition ease-out duration-200 delay-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full border-2 border-base-700"></div>
                        <div
                            class="absolute inset-0 w-12 h-12 rounded-full border-2 border-t-red border-r-orange border-b-transparent border-l-transparent animate-spin">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @stack('modals')
    @stack('scripts')
    @livewireScripts

    @if (!ce())
        <x-dynamic-component component="impersonate::banner" />
    @endif

</body>

</html>

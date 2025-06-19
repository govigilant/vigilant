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

<body class="font-text antialiased dark dark:bg-black min-w-screen h-screen flex" x-data="{ sidebarOpen: false }">
    <div class="fixed h-1 z-40 -top-px inset-x-0 bg-linear-to-r from-green to-red"></div>
    <x-layout.sidebar />

    <main class="dark:bg-black flex flex-col overflow-hidden flex-1">

        <x-layout.topbar />

        <div
            class="bg-base-900 rounded-tl-2xl rounded-tr-2xl lg:rounded-tr-none overflow-hidden shadow-inner-sm flex flex-col flex-1 pt-px">
            @if (isset($header))
                <header class="bg-base-950 px-8 py-4 text-neutral-50">
                    {{ $header }}
                </header>
            @endif

            <div class="px-4 sm:px-6 lg:px-8 pt-6 overflow-y-auto w-full max-h-full">
                <div class="pb-3">
                    <x-alert />
                </div>
                <x-banner />
                {{ $slot }}
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

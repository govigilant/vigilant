<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans antialiased dark dark:bg-black min-w-screen h-screen flex"  x-data="{ sidebarOpen: false }">
<div class="fixed h-1.5 z-40 -top-px inset-x-0 bg-gradient-to-r from-green to-red"></div>
<x-layout.sidebar/>

    <main class="dark:bg-black flex flex-col overflow-hidden flex-1">

        <x-layout.topbar/>


        <div class="bg-base-900 rounded-tl-2xl rounded-tr-2xl lg:rounded-tr-none overflow-hidden shadow-inner-sm flex flex-col flex-1 pt-px">
            @if (isset($header))
                <header class="bg-base-950 px-8 py-4 text-neutral-50">
                    {{ $header }}
                </header>
            @endif

            <div class="pt-3 px-8">
                <x-alert/>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 pt-6 overflow-y-auto w-full max-h-full">
                <x-banner/>
                {{ $slot }}
            </div>
        </div>

    </main>

@stack('modals')

@livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased dark dark:bg-black min-w-screen h-screen flex">
    <div class="fixed h-1.5 z-40 -top-px inset-x-0 bg-linear-to-r from-green to-red"></div>
    <main class="dark:bg-black flex flex-col overflow-hidden flex-1">
        <div
            class="bg-base-900 rounded-tl-2xl rounded-tr-2xl lg:rounded-tr-none overflow-hidden shadow-inner-sm flex flex-col flex-1 pt-px">
            <div class="px-4 sm:px-6 lg:px-8 pt-6 overflow-y-auto w-full max-h-full">
                {{ $slot }}
            </div>
        </div>
    </main>
    @livewireScripts

    @if (!ce())
        <x-dynamic-component component="impersonate::banner" />
    @endif
</body>

</html>

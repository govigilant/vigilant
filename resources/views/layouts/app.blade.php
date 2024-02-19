<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans antialiased dark dark:bg-black min-w-screen h-screen flex">
<div class="fixed h-1.5 z-40 -top-px inset-x-0 bg-gradient-to-r from-green to-red"></div>
<x-layout.sidebar/>

    <main class="dark:bg-black flex flex-col overflow-hidden flex-1">

        <x-layout.topbar/>


        <div class="bg-white rounded-tl-2xl overflow-hidden shadow-inner-sm flex flex-1 pt-px">
            <div class="px-4 sm:px-6 lg:px-8 overflow-y-auto w-full max-h-full bg-neutral-100">
                <x-banner/>

                @if (isset($header))
                    <header>
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{ $slot }}
            </div>
        </div>



    </main>

@stack('modals')

@livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="font-text antialiased dark dark:bg-base-black h-full">
<div class="fixed h-1.5 z-40 -top-px inset-x-0 bg-gradient-to-r from-green to-red"></div>
<main class="grid min-h-full place-items-center bg-base-black px-6 py-24 sm:py-32 lg:px-8">
    <div class="text-center">
        <p class="text-base font-semibold text-red">@yield('code')</p>
        <h1 class="mt-4 text-3xl font-header tracking-tight text-white sm:text-5xl">@yield('title')</h1>
        <p class="mt-6 text-base leading-7 text-base-100">@yield('message')</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="/"
               class="rounded-md bg-red px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-red-light focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red">@lang('Go back')</a>
        </div>
    </div>
</main>
</body>
</html>

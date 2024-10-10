<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    @vite(['resources/css/app.css'])
</head>
<body class="antialiased h-full">
<main class="grid min-h-full place-items-center bg-black px-6 py-24 sm:py-32 lg:px-8 bg-black">
    <div class="text-center">
        <p class="text-base font-semibold text-red">@yield('code')</p>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-5xl">@yield('title')</h1>
        <p class="mt-6 text-base leading-7 text-base-100">@yield('message')</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="/"
               class="rounded-md bg-red px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-light focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red">@lang('Go back')</a>
        </div>
    </div>
</main>
</body>
</html>

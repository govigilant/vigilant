<x-app-layout>
    <div class="text-center">
        <p class="font-semibold text-red text-4xl">@yield('code')</p>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-5xl">@yield('title')</h1>
        <p class="mt-6 text-base leading-7 text-base-100">@yield('message')</p>
    </div>
</x-app-layout>

@props(['body', 'current'])

<div
    {{ $attributes }}
    x-data="{
        open: false,
        toggle() {
            this.open = ! this.open;
        },
        close() {
            this.open = false;
        },
    }"
    x-on:click.away="close"
    x-on:keydown.escape.window="close"
>
    {{ $slot }}
    <div class="relative z-30">
        <div
            x-data="@js(['current' => $current])"
            x-show="open"
            x-transition
            x-cloak
            class="absolute top-1 right-0 bg-base-850 shadow-lg rounded-lg w-64 border border-base-700 transition-all duration-200"
        >
            {{ $body }}
        </div>
    </div>
</div>

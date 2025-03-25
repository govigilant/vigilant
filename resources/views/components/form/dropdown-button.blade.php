<a {{ $attributes->merge(['class' => 'cursor-pointer text-white text-md transition-all hover:bg-red']) }}>
    <li class="cursor-pointer text-white text-md transition-all p-3 hover:bg-red">
        {{ $slot }}
    </li>
</a>

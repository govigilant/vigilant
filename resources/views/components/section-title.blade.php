<div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 class="text-xl font-bold text-base-50 bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">{{ $title }}</h3>

        <p class="mt-2 text-sm leading-relaxed text-base-300">
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>

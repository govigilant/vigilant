<div class="flex items-center gap-x-3">
    <div class="flex-none rounded-full p-1 text-gray-500 bg-gray-100/10">
        @if($status === \Vigilant\Frontend\Integrations\Table\Enums\Status::Success)
            <div class="h-2 w-2 rounded-full bg-green-light"></div>
        @elseif($status === \Vigilant\Frontend\Integrations\Table\Enums\Status::Warning)
            <div class="h-2 w-2 rounded-full bg-orange-light animate-pulse"></div>
        @else
            <div class="h-2 w-2 rounded-full bg-red-light animate-pulse"></div>
        @endif
    </div>
    <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
        <span class="truncate">{{ $text }}</span>
    </h2>
</div>

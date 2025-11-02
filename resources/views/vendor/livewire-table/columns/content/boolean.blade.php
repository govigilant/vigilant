<div class="px-4 py-3">
    @if($value === null)
        <div class="mx-auto size-3 border-2 border-base-500 rounded-full"></div>
    @elseif($value)
        <div class="mx-auto size-3 border-2 border-green-500 rounded-full"></div>
    @else
        <div class="mx-auto size-3 border-2 border-red rounded-full"></div>
    @endif
</div>

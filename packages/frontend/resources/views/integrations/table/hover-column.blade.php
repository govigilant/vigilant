<div class="flex items-center gap-x-3">
    <span class="min-w-0 text-sm font-semibold leading-6 text-white has-tooltip">
         <span class='tooltip rounded shadow-lg p-2 bg-base-950 text-neutral-200 -mt-8'>
             @if($raw)
                 {!! $value !!}
             @else
                 {{ $value }}
             @endif
         </span>
        <span class="truncate">{{ $preview }}</span>
    </span>
</div>

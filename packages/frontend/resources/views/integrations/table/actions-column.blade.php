<div class="flex items-center justify-end gap-x-3 w-full px-4">
    @foreach ($actions as $action)
        @continue(!$action->isVisible($model))
        <span class="min-w-0 text-sm font-semibold leading-6 text-white has-tooltip"
            x-on:click.stop.prevent="$wire.runInlineAction('{{ $action->code }}', {{ json_encode($model->getKey()) }})">
            <span class="tooltip tooltip-left rounded-sm shadow-lg p-2 bg-base-950 text-neutral-200 mt-8">
                {{ $action->name }}
            </span>
            @svg($action->icon, 'h-5 w-5 text-base-200 hover:text-red cursor-pointer')
        </span>
    @endforeach
</div>

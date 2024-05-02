<div class="max-w-32">
    @if(isset($title))
        {!! $title !!}
    @endif
    <livewire:dynamic-component :is="$component" :data="$parameters" wire:key="{{ $component }}"/>
</div>

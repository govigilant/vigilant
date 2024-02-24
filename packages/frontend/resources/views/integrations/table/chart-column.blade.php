<div class="max-w-32">
    @if(isset($title))
        {!! $title !!}
    @endif
    @livewire($component, $parameters)
</div>

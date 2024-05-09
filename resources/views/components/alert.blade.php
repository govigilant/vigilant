@if(session('alert'))
    @php($type = session('alert-type'))

    <x-dynamic-component :component="$type->component()" :title="session('alert-title')" :message="session('alert-message')" />
@endif

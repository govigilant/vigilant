<p>{{ $description }}</p>

@if($viewUrl !== null)
    <a href="{{ $viewUrl }}">@lang('View in Vigilant')</a>
@endif

@if($url !== null && $urlTitle !== null)
    <a href="{{ $url }}">@lang($urlTitle)</a>
@endif


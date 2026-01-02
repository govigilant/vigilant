<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')"></x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<p style="margin: 0; background-color: #000000; padding: 16px; text-align: center; color: #ffffff;">
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
</p>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>

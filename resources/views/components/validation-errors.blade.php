@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'bg-red/10 border border-red/30 rounded-lg px-4 py-3']) }}>
        <div class="font-medium text-red-light">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

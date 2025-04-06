<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('lighthouse.index', ['monitor' => $result->lighthouse_monitor_id])" title="Lighthouse Result - {{ $result->created_at->toDateTimeString('minute') }}">

        </x-page-header>
    </x-slot>

    <div class="flex">
        <dl class="grid grid-cols-4 grid-rows-2 gap-4">
            @foreach(['performance', 'accessibility', 'best_practices', 'seo'] as $category)
                @php
                    $color = 'text-red';

                    $percentage = round($result[$category] * 100);

                    $color = match(true) {
                        $percentage > 80 => 'text-green-light',
                        $percentage > 60 => 'text-orange-light',
                        default => 'text-red-light'
                    };

                @endphp
                <div class="text-base-50 bg-base-950 text-center p-4 rounded-sm shadow-sm">
                    <dt class="truncate text-sm font-medium text-base-100">{{ str_replace('_', ' ', ucfirst($category)) }}</dt>
                    <dd class="mt-1 text-xl font-semibold tracking-tight {{ $color ?? 'text-base-50' }}">{{  $percentage . '%' }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div class="">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Audits') }}</h2>

        <livewire:lighthouse-result-audits-table :resultId="$result->id"/>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('cve.monitor.view', ['monitor' => $monitor])" :title="$cve->identifier" />
    </x-slot>

    <div class="space-y-4">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-frontend::stats-card :title="__('Identifier')">
                {{ $cve->identifier }}
            </x-frontend::stats-card>

            <x-frontend::stats-card :title="__('Score')">
                {{ $cve->score ?? 0 }}
            </x-frontend::stats-card>

            <x-frontend::stats-card :title="__('Published At')">
                {{ $cve->published_at->format('Y-m-d H:i:s') }}
            </x-frontend::stats-card>

            <x-frontend::stats-card :title="__('Last Modified')">
                {{ $cve->modified_at->format('Y-m-d H:i:s') }}
            </x-frontend::stats-card>
        </dl>

        <x-frontend::card>
            <div class="text-left">
                <p class="text-left">{{ $cve->description }}</p>

                <h3 class="text-lg font-bold mt-4">@lang('References')</h3>
                <ul class="list-disc pl-5">
                    @foreach (data_get($cve->data, 'cve.references.reference_data', []) as $reference)
                        <li>
                            <a href="{{ $reference['url'] }}" target="_blank">
                                {{ $reference['name'] ?? $reference['url'] }}
                            </a>
                        </li>
                    @endforeach
            </div>
        </x-frontend::card>
    </div>
</x-app-layout>

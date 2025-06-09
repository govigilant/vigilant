@props(['inline' => false])
<div @class(['w-full', 'sm:px-6 lg:px-8' => !$inline])>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="__('Import domain')" :back="route('dns.index')">
            </x-page-header>
        </x-slot>
    @endif

    <div class="gap-4 grid grid-cols-2">

        <div>
            <x-form.text field="domain" name="Domain" description="Domain to lookup DNS records for" :live="false" />
        </div>

        <div class="pt-2">
            <x-form.button class="bg-blue hover:bg-blue-light disabled:opacity-50" wire:loading.attr="disabled"
                type="button" wire:click="lookup">
                @lang('Import')
            </x-form.button>
        </div>

    </div>

    <div class="mt-6" wire:loading>
        <span class="text-xs text-base-300">@lang('Looking up DNS records')</span>
    </div>
    @if ($records === [])
        @if ($noRecords)
            <p class="text-md text-base-200 mt-4">@lang('No records found')</p>
        @endif
    @else
        <div class="mt-6 inline-block min-w-full" wire:loading.remove>

            <table class="min-w-full divide-y divide-base-400">
                <thead>
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">
                            @lang('Type')</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">
                            @lang('Host')</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">
                            @lang('Value')</th>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">
                            <span class="sr-only">@lang('Remove')</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-600">
                    @foreach ($records as $index => $record)
                        @continue(in_array($index, $deleted))

                        <tr class="hover:bg-base-800">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-base-300 sm:pl-0">
                                {{ $record['type']->name }}</td>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-base-300 sm:pl-0">
                                {{ $record['host'] }}</td>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-base-300 sm:pl-0">
                                {{ $record['value'] }}</td>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-base-300 sm:pl-0">
                                <x-form.button class="bg-red hover:bg-red-light" type="button"
                                    wire:loading.attr="disabled" wire:click="remove({{ $index }})">
                                    @lang('Remove')
                                </x-form.button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!$inline && count($records) > 0)
            <div class="mt-4 mb-12 flex justify-end">
                <x-form.button class="bg-green hover:bg-green-light" type="button" wire:loading.attr="disabled"
                    wire:click="save()">
                    @lang('Save')
                </x-form.button>
            </div>
        @endif

    @endif
</div>

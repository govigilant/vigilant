<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header title="Import">
            </x-page-header>
        </x-slot>
    @endif

    <div>
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            @if ($validatedDomains === [])
                <form wire:submit.prevent="confirm">
                    <div class="grid grid-cols-2 mb-4">
                        <div>
                            <label for="urls"
                                class="block text-sm font-medium leading-6 text-white">@lang('Domains')</label>
                            <span class="text-neutral-400 text-xs">@lang('Add domains or URLs to import, one per line')</span>
                        </div>
                        <div class="mt-2">
                            <div
                                class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                                <textarea name="urls" id="urls" wire:model="urls" wire:loading.attr="disabled" rows="10"
                                    class="flex-1 border-0 bg-transparent py-1.5 text-white focus:ring-0 sm:text-sm sm:leading-6 disabled:bg-base-950"></textarea>
                            </div>

                            @error('urls')
                                <span class="text-red">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base-50 text-lg font-bold mb-1">@lang('Monitors to enable')</h3>
                        @foreach ($availableMonitors as $key => $label)
                            <x-form.checkbox field="monitors.{{ $key }}" name="{{ $label }}" />
                        @endforeach

                        @error('monitors')
                            <span class="text-red">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-form.submit-button dusk="submit-button" submitText="Import" />
                </form>
            @else
                <div class="text-base-100 space-y-2">
                    <h3 class="text-lg font-bold">@lang('Domains to import (:count):', ['count' => count($validatedDomains)])</h3>
                    <ul class="list-disc max-h-[300px] overflow-y-scroll">
                        @foreach ($validatedDomains as $domain)
                            <li>{{ $domain }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-4 w-full flex justify-end gap-2">
                        <x-form.button class="bg-red hover:bg-red-light" wire:click="cancel"
                            wire:loading.attr="disabled">@lang('Cancel')</x-form.button>
                        <x-form.button class="bg-blue hover:bg-blue-light" wire:click="import"
                            wire:loading.attr="disabled">@lang('Import')</x-form.button>
                    </div>
                    <div>
            @endif
        </div>

    </div>

</div>

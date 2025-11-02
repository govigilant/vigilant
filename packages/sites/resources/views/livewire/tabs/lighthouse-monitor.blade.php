<div>

    <div class="mb-4">
        <h2 class="text-lg text-base-100">@lang('Pages')</h2>
        <p class="text-sm text-base-200">@lang('Select which pages have to be monitored by Lighthouse. You may add multiple URLs.')</p>
    </div>

    <div class="grid grid-cols-3 space-y-2">

        <h3 class="text-md text-base-200 font-bold">@lang('URL')</h3>
        <h3 class="text-md text-base-200 font-bold">@lang('Interval')</h3>
        <span></span>


        @foreach ($monitors as $index => $monitor)
            <div class="pr-4">
                <input type="text" wire:model.live="monitors.{{ $index }}.url"
                    class="mt-2 w-full block rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                @error("monitors.$index.url")
                    <span class="text-red">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <select wire:model.live="monitors.{{ $index }}.interval"
                    class="mt-2 block rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                    @foreach (config('lighthouse.intervals') as $interval => $label)
                        <option value="{{ $interval }}">@lang($label)</option>
                    @endforeach
                </select>
            </div>

            @if (!blank($monitor['id'] ?? null))
                <div class="flex justify-end items-center">
                    <x-form.button href="{{ route('lighthouse.index', ['monitor' => $monitor['id']]) }}" target="_blank"
                        class="bg-green">@lang('View')</x-form.button>
                </div>
            @else
                <span></span>
            @endif
        @endforeach

    </div>

    <div class="mt-4">
        <x-form.button type="button" wire:click="addPage" class="bg-gradient-to-r from-red via-orange to-red">@lang('Add Lighthouse Monitor')</x-form.button>
    </div>

</div>

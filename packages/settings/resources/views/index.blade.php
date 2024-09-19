<x-slot name="header">
    <x-page-header title="Settings">
    </x-page-header>
</x-slot>
<div>

    <div x-data="{ selectedTab: @entangle('tab') }">
        <div class="max-w-7xl mb-4">
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">{{ __('Select a tab') }}</label>
                <select name="tabs"
                        x-model="selectedTab"
                        class="block w-full rounded-md border-none bg-white/5 py-2 pl-3 pr-10 text-base text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm">
                    @foreach($tabs as $key => $data)
                        <option value="{{ $key }}">{{ $data['title']  }}</option>
                    @endforeach
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="flex border-b border-white/10 py-4">
                    <ul role="list"
                        class="flex min-w-full flex-none gap-x-6 px-2 text-sm font-semibold leading-6 text-gray-400">
                        @foreach($tabs as $key => $data)
                            <li>
                                    <span
                                        x-on:click="selectedTab = '{{ $key }}'"
                                        :class="{ 'text-red': selectedTab == '{{ $key }}'}"
                                        class="cursor-pointer select-none">{{$data['title'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>

        <div>
            @foreach($tabs as $key => $data)
                <div x-show="selectedTab == '{{ $key }}'">
                    @if(array_key_exists('component', $data))
                        <livewire:dynamic-component :is="$data['component']"
                                                    wire:key="{{ $key }}"/>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>

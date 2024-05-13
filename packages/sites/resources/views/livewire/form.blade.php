<div>
    <x-slot name="header">
        <x-page-header title="Add Site" :back="route('sites')"></x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text class="sm:col-span-2"
                         field="form.url"
                         name="URL"
                         :disabled="$updating"
                         description="The URL of the site that you want to add."
                         placeholder="{{ config('app.url') }}"/>

            @if($updating)
                <div x-data="{ selectedTab: '{{ \Illuminate\Support\Arr::first(array_keys($tabs)) }}' }">
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
                                <livewire:dynamic-component :is="$data['component']"
                                                            :site="$site"
                                                            wire:key="{{ $key }}"/>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif

            <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>

        </div>
    </form>
</div>

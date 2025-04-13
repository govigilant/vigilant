<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Site ' . $site->url : 'Add Site'" :back="$updating ? route('site.view', ['site' => $site]) : route('sites')"></x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text class="sm:col-span-2" field="form.url" name="URL" :disabled="$updating"
                description="The URL of the site that you want to add" placeholder="{{ config('app.url') }}" />

            @if ($updating)
                <div x-data="{ selectedTab: '{{ \Illuminate\Support\Arr::first(array_keys($tabs)) }}' }">
                    <div class="max-w-7xl mb-4">
                        <div class="sm:hidden">
                            <label for="tabs" class="sr-only">{{ __('Select a tab') }}</label>
                            <select name="tabs" x-model="selectedTab"
                                class="block w-full rounded-md border-none bg-white/5 py-2 pl-3 pr-10 text-base text-white shadow-xs ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm">
                                @foreach ($tabs as $key => $data)
                                    @can('create', $data['model'])
                                        <option value="{{ $key }}">{{ $data['title'] }}</option>
                                    @endcan
                                @endforeach
                            </select>
                        </div>
                        <div class="hidden sm:block">
                            <nav class="flex border-b border-base-700">
                                <ul class="flex min-w-full gap-x-2 text-sm font-semibold text-gray-400">
                                    @foreach ($tabs as $key => $data)
                                        @can('create', $data['model'])
                                            <li x-on:click="selectedTab = '{{ $key }}'"
                                                :class="{ 'text-red bg-base-800 rounded-t-lg': selectedTab ==
                                                        '{{ $key }}' }"
                                                class="cursor-pointer select-none px-2.5 py-1.5 hover:bg-base-800 hover:rounded-t-lg">
                                                {{ $data['title'] }}
                                            </li>
                                        @endcan
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    </div>

                    <div>
                        @foreach ($tabs as $key => $data)
                            @can('create', $data['model'])
                                <div x-show="selectedTab == '{{ $key }}'">
                                    <livewire:dynamic-component :is="$data['component']" :site="$site"
                                        wire:key="{{ $key }}" />
                                </div>
                            @endcan
                        @endforeach
                    </div>
                </div>

            @endif

            @if (!$inline)
                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
            @endif

        </div>
    </form>
</div>

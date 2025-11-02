<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Site ' . $site->url : 'Add Site'" :back="$updating ? route('site.view', ['site' => $site]) : route('sites')"></x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    <x-form.text class="sm:col-span-2" field="form.url" name="URL" :disabled="$updating"
                        description="The URL of the site that you want to add" placeholder="{{ config('app.url') }}" />

                    @if ($updating)
                        <div x-data="{ selectedTab: '{{ \Illuminate\Support\Arr::first(array_keys($tabs)) }}' }">
                            <div class="mb-4">
                                <div class="sm:hidden">
                                    <label for="tabs" class="sr-only">{{ __('Select a tab') }}</label>
                                    <select name="tabs" x-model="selectedTab"
                                        class="block w-full rounded-lg border border-base-700 bg-base-850 py-2.5 pl-3 pr-10 text-base-100 shadow-sm focus:border-red focus:ring-2 focus:ring-red/20 transition-colors">
                                        @foreach ($tabs as $key => $data)
                                            <option value="{{ $key }}">{{ $data['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="hidden sm:block">
                                    <nav class="flex gap-2 p-1 bg-base-850/50 rounded-lg border border-base-700/50 backdrop-blur-sm">
                                        @foreach ($tabs as $key => $data)
                                            <button
                                                type="button"
                                                x-on:click="selectedTab = '{{ $key }}'"
                                                :class="selectedTab == '{{ $key }}' 
                                                    ? 'bg-gradient-to-r from-red to-orange text-white shadow-lg shadow-red/20' 
                                                    : 'text-base-300 hover:text-base-100 hover:bg-base-800/50'"
                                                class="flex-1 px-4 py-2.5 text-sm font-medium rounded-md transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red">
                                                {{ $data['title'] }}
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>
                            </div>

                            <div>
                                @foreach ($tabs as $key => $data)
                                    <div x-show="selectedTab == '{{ $key }}'"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <livewire:dynamic-component :is="$data['component']" :site="$site"
                                            wire:key="{{ $key }}" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!$inline)
                        <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
                    @endif
                </div>
            </x-card>
        </div>
    </form>
</div>

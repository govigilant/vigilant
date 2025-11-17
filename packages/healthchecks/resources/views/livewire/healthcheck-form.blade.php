@props(['updating' => false])
<div>
    <x-slot name="header">
        <x-page-header :title="$updating
            ? __('Edit Healthcheck - :domain', ['domain' => $healthcheck->domain])
            : __('Add Healthcheck')" :back="route('healthchecks.index')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    <x-form.checkbox field="form.enabled" name="Enabled"
                        description="Enable or disable this healthcheck" />

                    <x-form.text field="form.domain" name="URL"
                        description="URL of your service (e.g. https://govigilant.io)" />

                    <x-form.select field="form.interval" name="Interval"
                        description="Choose how often this healthcheck should run">
                        @foreach (config('healthchecks.intervals') as $interval => $label)
                            <option value="{{ $interval }}">@lang($label)</option>
                        @endforeach
                    </x-form.select>

                    <div x-data="{
                        selectedType: $wire.entangle('form.type'),
                        customizeEndpoint: false,
                        showAll: false,
                        searchQuery: '',
                        totalPlatforms: {{ count(\Vigilant\Healthchecks\Enums\Type::cases()) }},
                        platformEndpoints: {
                            @foreach (\Vigilant\Healthchecks\Enums\Type::cases() as $type)
                                '{{ $type->value }}': {{ $type->endpoint() ? "'" . $type->endpoint() . "'" : "''" }}, @endforeach
                        },
                        get hasMore() {
                            return this.totalPlatforms > 12;
                        }
                    }" x-init="$watch('selectedType', value => {
                        if (platformEndpoints[value] !== undefined) {
                            $wire.set('form.endpoint', platformEndpoints[value]);
                        }
                    })">
                        <div>
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        @lang('Platform')
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        @lang('Select your platform, if you have not installed the Vigilant healthcheck module then select "Endpoint".')
                                    </p>
                                </div>
                                <div class="w-64">
                                    <input type="text" x-model="searchQuery" placeholder="Search platforms..."
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach (\Vigilant\Healthchecks\Enums\Type::cases() as $type)
                                    <button type="button" @click="selectedType = '{{ $type->value }}'"
                                        x-show="searchQuery === '' || '{{ strtolower($type->label()) }}'.includes(searchQuery.toLowerCase())"
                                        x-transition
                                        class="relative flex flex-col items-center justify-center p-4 rounded-lg border transition-all hover:shadow-md"
                                        :class="selectedType === '{{ $type->value }}'
                                            ?
                                            'border-indigo-500 bg-indigo-100 dark:bg-indigo-500/20 dark:border-indigo-400' :
                                            'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 bg-gray-50 dark:bg-gray-800/50'"
                                        x-data="{ index: {{ $loop->index }} }"
                                        x-show.transition="(showAll || index < 12) && (searchQuery === '' || '{{ strtolower($type->label()) }}'.includes(searchQuery.toLowerCase()))">
                                        <x-icon name="{{ $type->icon() }}" class="w-8 h-8 mb-2 transition-colors"
                                            x-bind:class="selectedType === '{{ $type->value }}' ?
                                                'text-indigo-700 dark:text-indigo-300' :
                                                'text-gray-700 dark:text-gray-300'" />
                                        <span class="text-xs font-semibold transition-colors text-center"
                                            x-bind:class="selectedType === '{{ $type->value }}' ?
                                                'text-indigo-700 dark:text-indigo-300' :
                                                'text-gray-700 dark:text-gray-300'">
                                            {{ $type->label() }}
                                        </span>
                                        <x-icon name="heroicon-s-check-circle"
                                            class="absolute top-2 right-2 w-5 h-5 text-indigo-700 dark:text-indigo-300 transition-opacity"
                                            x-bind:class="selectedType === '{{ $type->value }}' ? 'opacity-100' : 'opacity-0'" />
                                    </button>
                                @endforeach
                            </div>

                            <div x-show="hasMore && !showAll" class="mt-3 text-center">
                                <button type="button" @click="showAll = true"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                    <span x-text="`Show ${totalPlatforms - 12} more platforms`"></span>
                                    <x-icon name="heroicon-s-chevron-down" class="ml-1 w-4 h-4" />
                                </button>
                            </div>

                            <div x-show="showAll && hasMore" class="mt-3 text-center">
                                <button type="button" @click="showAll = false"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                    @lang('Show less')
                                    <x-icon name="heroicon-s-chevron-up" class="ml-1 w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 min-h-[88px]">
                            <div x-show="selectedType === 'endpoint'">
                                <x-form.text field="form.endpoint" name="Endpoint"
                                    description="URL path to check (e.g., /health). Must return HTTP 200 status for a successful check." />
                            </div>

                            <div x-show="selectedType !== 'endpoint'">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            @lang('Customize endpoint')
                                        </label>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            @lang('Override the default endpoint path for this platform')
                                        </p>
                                    </div>
                                    <button type="button" @click="customizeEndpoint = !customizeEndpoint"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                                        :class="customizeEndpoint ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                                        role="switch" :aria-checked="customizeEndpoint">
                                        <span
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="customizeEndpoint ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>
                                </div>

                                <div x-show="customizeEndpoint" x-transition class="mt-4">
                                    <x-form.text field="form.endpoint" name="Endpoint"
                                        description="Custom endpoint path (leave empty to use default: api/vigilant/health)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 items-center">
                        <x-form.submit-button dusk="submit-button" wire:loading.attr="disabled" :submitText="$updating ? 'Save' : 'Create'" />
                    </div>
                </div>
            </x-card>
        </div>
    </form>
</div>

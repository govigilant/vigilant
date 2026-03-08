<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? __('Edit Crawler :url', ['url' => $crawler->start_url]) : __('Add Crawler')" :back="$updating ? route('crawler.view', ['crawler' => $crawler]) : route('crawler.index')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    @if (!$inline)
                        <x-form.checkbox field="form.enabled" name="Enabled" description="Enable or disable the crawler" />
                    @endif

                    <x-form.text field="form.start_url" name="Start URL" description="Enter the URL to start crawling" />

                    <x-form.text-list field="form.sitemaps" name="Sitemaps" description="Sitemaps to retrieve URLs from"
                        placeholder="Sitemap URL" :items="$form->sitemaps" />

                    <div class="grid grid-cols-2" x-data="{
                        type: @entangle('form.settings.scheduleConfig.type'),
                    }">
                        <div>
                            <label for="schedule"
                                class="block text-sm font-medium leading-6 text-base-100">@lang('Schedule')</label>
                            <span class="text-base-400 text-xs">@lang('Choose how often the website should be crawled')</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <select x-model="type"
                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                                <option value="daily">@lang('Daily')</option>
                                <option value="weekly">@lang('Weekly')</option>
                                <option value="monthly">@lang('Monthly')</option>
                            </select>

                            <span x-show="type !== 'daily'" class="text-base-100">@lang('On')</span>

                            <select x-show="type === 'weekly'" wire:model.live="form.settings.scheduleConfig.weekDay"
                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                                @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                                    <option value="{{ $index }}">{{ $day }}</option>
                                @endforeach
                            </select>

                            <select x-show="type === 'monthly'" wire:model.live="form.settings.scheduleConfig.monthDay"
                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                                @for ($i = 0; $i < 31; $i++)
                                    <option value="{{ $i }}">Day {{ $i + 1 }}</option>
                                @endfor
                            </select>

                            <span class="text-base-100">@lang('At')</span>

                            <select wire:model.live="form.settings.scheduleConfig.hour"
                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                                @for ($i = 0; $i < 24; $i++)
                                    <option value="{{ $i }}">{{ sprintf('%02d', $i) }}:00</option>
                                @endfor

                            </select>

                        </div>
                        <div class="mt-1">
                            @error('schedule')
                                <span class="text-red">{{ $message }}</span>
                            @enderror
                            @if ($invalidDay)
                                <span class="text-orange">@lang('Warning! Setting the day above 28 will cause it to NOT run montly')</span>
                            @endif
                        </div>

                    </div>


                    @if (!$inline)
                        <div x-data="{
                            open: false,
                            platform: '',
                            platforms: {{ Js::from(collect(config('crawler.platform_blacklists'))->map(fn ($p) => ['label' => $p['label'], 'patterns' => implode("\n", $p['patterns'])])) }},
                            applyPlatform() {
                                if (this.platform === '') return;
                                this.$refs.urlBlacklist.value = this.platforms[this.platform].patterns;
                                this.$refs.urlBlacklist.dispatchEvent(new Event('input'));
                                this.$refs.urlBlacklist.dispatchEvent(new Event('change'));
                            },
                        }" class="flex flex-col gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col justify-center">
                                    <label class="block text-base font-semibold leading-6 text-base-50">@lang('Platform preset')</label>
                                    <span class="text-base-400 text-sm mt-1">@lang('Optionally pre-fill the URL blacklist with known paths for a platform.')</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <select x-model="platform" @change="applyPlatform()"
                                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
                                        <option value="">@lang('None')</option>
                                        <template x-for="[key, p] in Object.entries(platforms)" :key="key">
                                            <option :value="key" x-text="p.label"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-base-700 pt-4">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-2 text-sm font-medium text-base-400 hover:text-base-100 transition-colors duration-200">
                                    <div class="transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''">
                                        @svg('phosphor-caret-down', 'w-4 h-4')
                                    </div>
                                    @lang('Advanced')
                                </button>

                                <div x-show="open" x-cloak x-collapse class="mt-4 flex flex-col gap-4">
                                    <x-form.textarea
                                        field="form.url_blacklist"
                                        name="URL Blacklist"
                                        description="One regex pattern per line. URLs matching any pattern will not be crawled."
                                        :rows="6"
                                        placeholder="/admin/&#10;/private/.*"
                                        xRef="urlBlacklist"
                                    />
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </form>
</div>

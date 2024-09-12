<div>
    @if(!$inline)
    <x-slot name="header">
        <x-page-header
            :title="$updating ? __('Edit Crawler :url', ['url' => $crawler->start_url])  : __('Add Crawler')"
            :back="route('crawler.index')">
        </x-page-header>
    </x-slot>
    @endif

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text
                field="form.start_url"
                name="Start URL"
                description="Enter the URL to start crawling"
            />

            <x-form.text-list
                field="form.sitemaps"
                name="Sitemaps"
                description="Sitemaps to retrieve URLs from"
                placeholder="Sitemap URL"
                :items="$form->sitemaps"
            />

            <div class="grid grid-cols-2" x-data="{
                    type: @entangle('form.settings.scheduleConfig.type'),
                }">
                <div>
                    <label for="schedule"
                           class="block text-sm font-medium leading-6 text-white">@lang('Schedule')</label>
                    <span
                        class="text-neutral-400 text-xs">@lang('Choose how often the website should be crawled')</span>
                </div>
                <div class="flex items-center gap-2">
                    <select x-model="type"
                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                        <option value="daily">@lang('Daily')</option>
                        <option value="weekly">@lang('Weekly')</option>
                        <option value="monthly">@lang('Monthly')</option>
                    </select>

                    <span x-show="type !== 'daily'" class="text-white">@lang('On')</span>

                    <select
                        x-show="type === 'weekly'"
                        wire:model.live="form.settings.scheduleConfig.weekDay"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                            <option value="{{ $index }}">{{ $day }}</option>
                        @endforeach
                    </select>

                    <select
                        x-show="type === 'monthly'"
                        wire:model.live="form.settings.scheduleConfig.monthDay"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                        @for($i = 0; $i < 28; $i++)
                            <option value="{{ $i }}">Day {{ ($i+1) }}</option>
                        @endfor
                    </select>

                    <span class="text-white">@lang('At')</span>

                    <select
                        wire:model.live="form.settings.scheduleConfig.hour"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                        @for($i = 0; $i < 24; $i++)
                            <option value="{{ $i }}">{{ sprintf("%02d", $i) }}:00</option>
                        @endfor

                    </select>

                </div>
                @error('schedule') <span class="text-red">{{ $message }}</span> @enderror

            </div>


            @if(!$inline)
                <div class="flex justify-end gap-4">
                    <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>
                </div>
            @endif
        </div>
    </form>
</div>

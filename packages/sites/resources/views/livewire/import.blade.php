<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header title="Import" :back="route('sites')">
            </x-page-header>
        </x-slot>
    @endif

    <div class="max-w-7xl mx-auto">
        <x-card>
            <div class="flex flex-col gap-6">
                @if ($validatedDomains === [])
                    <form wire:submit.prevent="confirm">
                        <!-- Domains Input -->
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="urls" class="block text-base font-semibold leading-6 text-base-100">
                                        @lang('Your Websites')
                                    </label>
                                    <span class="text-base-400 text-sm mt-1">@lang('Add domains or URLs to import, one per line')</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
                                    <textarea 
                                        name="urls" 
                                        id="urls" 
                                        wire:model="urls" 
                                        wire:loading.attr="disabled" 
                                        rows="8"
                                        placeholder="example.com&#10;https://mysite.com&#10;anotherdomain.org"
                                        class="flex-1 border-0 bg-transparent py-3 px-4 text-base-100 placeholder:text-base-600 focus:ring-0 sm:text-sm sm:leading-6 disabled:bg-base-950 disabled:text-base-500"></textarea>
                                </div>

                                @error('urls')
                                    <span class="text-red text-sm mt-2 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Monitor Selection -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <h3 class="text-base font-semibold text-base-100 mb-1">@lang('Monitoring Features')</h3>
                                <p class="text-sm text-base-400">@lang('Select which monitors to enable for your imported sites')</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($availableMonitors as $key => $monitor)
                                    <label 
                                        x-data="{ enabled: $wire.entangle('monitors.{{ $key }}').live }"
                                        :class="enabled ? 'border-blue bg-base-850/50' : 'border-base-700 bg-base-850'"
                                        class="relative flex items-start gap-4 p-4 rounded-lg border hover:border-base-600 transition-all duration-200 cursor-pointer group">
                                        <button type="button"
                                            role="switch"
                                            x-on:click="enabled = !enabled"
                                            :aria-checked="enabled.toString()"
                                            :class="enabled ? 'bg-gradient-to-r from-red to-orange' : 'bg-base-700'"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-2 focus:ring-offset-base-900 mt-0.5">
                                            <span class="sr-only">{{ $monitor['label'] }}</span>
                                            <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-base-50 shadow-lg ring-0 transition duration-200 ease-in-out"></span>
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-base-100 font-semibold block leading-tight">{{ $monitor['label'] }}</span>
                                            <span class="text-xs text-base-400 block mt-1 leading-relaxed">{{ $monitor['description'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error('monitors')
                                <span class="text-red text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red to-orange hover:from-red-light hover:to-orange-light text-base-100 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 shadow-lg hover:shadow-red/30">
                                <span wire:loading.remove wire:target="confirm">@lang('Continue') →</span>
                                <span wire:loading wire:target="confirm">@lang('Processing...')</span>
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Confirmation Step -->
                    <div class="text-base-100 space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-base-100 mb-2">
                                @lang('Ready to import :count sites', ['count' => count($validatedDomains)])
                            </h3>
                            <p class="text-base-400">@lang('Review the domains below before importing')</p>
                        </div>
                        
                        <div class="bg-base-850 border border-base-700 rounded-lg p-4 max-h-[400px] overflow-y-auto">
                            <ul class="space-y-2">
                                @foreach ($validatedDomains as $domain)
                                    <li class="flex items-center gap-2 text-base-200">
                                        <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>{{ $domain }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                wire:click="cancel"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-3 bg-base-800 hover:bg-base-700 text-base-100 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-base-700 focus:ring-offset-base-900">
                                @lang('← Back')
                            </button>
                            <button type="button"
                                wire:click="import"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red to-orange hover:from-red-light hover:to-orange-light text-base-100 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900 shadow-lg hover:shadow-red/30">
                                <span wire:loading.remove wire:target="import">@lang('Import Sites') ✓</span>
                                <span wire:loading wire:target="import">@lang('Importing...')</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</div>

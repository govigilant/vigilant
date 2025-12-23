<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Uptime Monitor - ' . $monitor->name : 'Add Uptime Monitor'" :back="$updating ? route('uptime.monitor.view', ['monitor' => $monitor]) : route('uptime')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    @if (!$inline)
                        <x-form.checkbox field="form.enabled" name="Enabled" description="Enable or disable this monitor" />
                    @endif
                    <x-form.text field="form.name" name="Name" description="Friendly name for this monitor" />

                    <div class="relative">
                        <x-form.select field="form.type" name="Monitor Type"
                            description="Choose how this monitor should check if the service is up">
                            @foreach (\Vigilant\Uptime\Enums\Type::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </x-form.select>
                        
                        <!-- Subtle inline loading indicator -->
                        <div wire:loading wire:target="form.type" 
                             class="absolute right-2 top-9 flex items-center gap-2 text-xs text-base-400">
                            <svg class="w-4 h-4 animate-spin text-blue" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Loading...</span>
                        </div>
                    </div>

                    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="form.type" class="transition-opacity duration-200">
                        @if ($form->type === \Vigilant\Uptime\Enums\Type::Http->value)
                            <x-form.text field="form.settings.host" name="Host" description="HTTP Host"
                                placeholder="{{ config('app.url') }}" />
                        @elseif ($form->type === \Vigilant\Uptime\Enums\Type::Ping->value)
                            <x-form.text field="form.settings.host" name="Host" description="Host or IP address of the service"
                                placeholder="{{ config('app.url') }} or 1.1.1.1" />

                            <x-form.number field="form.settings.port" name="Port" description="Port to check" />
                        @endif
                    </div>

                    <x-form.select field="form.interval" name="Interval"
                        description="Choose how often this monitor should check the service">
                        @foreach (config('uptime.intervals') as $interval => $label)
                            <option value="{{ $interval }}">@lang($label)</option>
                        @endforeach
                    </x-form.select>

                    <x-form.number field="form.retries" name="Retries"
                        description="Amount of retries before marking the service as down" />

                    <x-form.number field="form.timeout" name="Timeout" description="Timeout for connecting to the service" />

                    <div class="border-t border-base-200 pt-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col justify-center">
                                <p class="block text-base font-semibold leading-6 text-base-50">Set location manually</p>
                                <span class="text-base-400 text-sm mt-1">Provide the country and coordinates instead of detecting them automatically.</span>
                            </div>
                            <div class="flex flex-col justify-center">
                                <div class="flex items-center h-10">
                                    <button type="button"
                                        role="switch"
                                        x-data="{ automatic: $wire.entangle('form.geoip_automatic').live }"
                                        x-on:click="automatic = !automatic"
                                        :aria-checked="(!automatic).toString()"
                                        :class="(!automatic) ? 'bg-gradient-to-r from-red to-orange' : 'bg-base-700'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red focus:ring-offset-2 focus:ring-offset-base-900">
                                        <span class="sr-only">Set location manually</span>
                                        <span :class="(!automatic) ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-base-50 shadow-lg ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </div>
                                @error('form.geoip_automatic') <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if (! $form->geoip_automatic)
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mt-4">
                                <x-form.text field="form.country" name="Country"
                                    description="Two-letter country code (e.g. US)" />

                                <x-form.number field="form.latitude" name="Latitude"
                                    description="Between -90 and 90"
                                    step="any" min="-90" max="90" placeholder="43.6532" />

                                <x-form.number field="form.longitude" name="Longitude"
                                    description="Between -180 and 180"
                                    step="any" min="-180" max="180" placeholder="-79.3832" />
                            </div>
                        @endif
                    </div>

                    @if (!$inline)
                        <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
                    @endif
                </div>
            </x-card>
        </div>
    </form>
</div>

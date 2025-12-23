<div>
    <x-slot name="header">
        <x-page-header :title="__('Setup Healthcheck - :domain', ['domain' => $healthcheck->domain])" :back="route('healthchecks.view', ['healthcheck' => $healthcheck])">
        </x-page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-card>
            <div class="flex flex-col gap-6">
                @if ($isNew)
                    <div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">
                            {{ __('Healthcheck Created Successfully!') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('Your healthcheck has been created. Follow the instructions below to integrate it with your platform.') }}
                        </p>
                    </div>
                @endif

                <div class="{{ $isNew ? 'border-t border-gray-200 dark:border-gray-700 pt-6' : '' }}">
                    <h4 class="text-md font-semibold mb-3 text-gray-900 dark:text-gray-100">
                        {{ __('Integration Instructions') }}</h4>

                    <div class="space-y-4">
                        <livewire:healthcheck-token-editor :healthcheck="$healthcheck" :key="'healthcheck-token-editor-' . $healthcheck->getKey()" />

                        @includeIf('healthchecks::platforms.' . $healthcheck->type->value, ['healthcheck' => $healthcheck])
                    </div>
                </div>

                <div class="flex justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <a href="{{ route('healthchecks.index') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        {{ __('Go to Healthchecks') }}
                    </a>
                </div>
            </div>
        </x-card>
    </div>

</div>

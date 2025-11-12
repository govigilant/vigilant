<div>
    <x-slot name="header">
        <x-page-header :title="__('Setup Healthcheck - :domain', ['domain' => $healthcheck->domain])" :back="route('healthchecks.index')">
        </x-page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-card>
            <div class="flex flex-col gap-6">
                @if($isNew)
                <div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Healthcheck Created Successfully!') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Your healthcheck has been created. Follow the instructions below to integrate it with your platform.') }}
                    </p>
                </div>
                @endif

                <div class="{{ $isNew ? 'border-t border-gray-200 dark:border-gray-700 pt-6' : '' }}">
                    <h4 class="text-md font-semibold mb-3 text-gray-900 dark:text-gray-100">{{ __('Integration Instructions') }}</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Your Healthcheck Token') }}
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    readonly 
                                    value="{{ $healthcheck->token }}"
                                    class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-mono text-sm text-gray-900 dark:text-gray-100"
                                    id="token-input"
                                />
                                <button 
                                    type="button"
                                    onclick="copyToken(this)"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors duration-200"
                                    id="copy-button"
                                >
                                    <span id="copy-text">{{ __('Copy') }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('Setup Steps') }}</h5>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-200">
                                <li>{{ __('Copy the token above') }}</li>
                                <li>{{ __('Add this token to your platform\'s configuration') }}</li>
                                @if($healthcheck->type->value === 'endpoint')
                                    <li>{{ __('Ensure your endpoint :endpoint returns an HTTP 200 status', ['endpoint' => $healthcheck->endpoint]) }}</li>
                                @else
                                    <li>{{ __('Configure your Laravel health checks module') }}</li>
                                @endif
                                <li>{{ __('The healthcheck will run automatically at the configured interval') }}</li>
                            </ol>
                        </div>

                        @if($healthcheck->type->value === 'endpoint')
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Endpoint Details') }}</h5>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Domain:') }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $healthcheck->domain }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Endpoint:') }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $healthcheck->endpoint }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Check Interval:') }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Every :interval minutes', ['interval' => $healthcheck->interval]) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <a 
                        href="{{ route('healthchecks.index') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium"
                    >
                        {{ __('Go to Healthchecks') }}
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    <script>
        function copyToken(button) {
            const input = document.getElementById('token-input');
            const copyText = document.getElementById('copy-text');
            
            navigator.clipboard.writeText(input.value).then(function() {
                // Success - update button
                copyText.textContent = '{{ __('Copied!') }}';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    copyText.textContent = '{{ __('Copy') }}';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</div>

<div class="space-y-4">
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('Setup Steps') }}</h5>
        <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-200">
            <li>{{ __('Copy the token above') }}</li>
            <li>{{ __('Add this token to your platform\'s configuration') }}</li>
            <li>{{ __('Ensure your endpoint :endpoint returns an HTTP 200 status', ['endpoint' => $healthcheck->endpoint]) }}
            </li>
            <li>{{ __('The healthcheck will run automatically at the configured interval') }}</li>
        </ol>
    </div>

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
</div>

<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('Setup your Magento 2 healthcheck') }}</h5>
    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-200">
        <li><a class="text-blue-600 dark:text-blue-400 underline" target="_blank"
                href="https://github.com/govigilant/magento2-healthchecks">{{ __('Install the Magento 2 healthcheck module') }}</a>
        </li>
        <li>{{ __('In your Magento 2 backend, go to System > Integrations') }}</li>
        <li>{{ __('Create a integration with permissions for the "Health Endpoint"') }}</li>
        <li>{{ __('Activate the integration and paste the access token here') }}</li>
    </ol>
</div>

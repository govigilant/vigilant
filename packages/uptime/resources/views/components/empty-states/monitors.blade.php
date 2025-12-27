<x-frontend::empty-state
    :title="__('No Uptime Monitors Yet')"
    :description="__('Create your first uptime monitor to start tracking availability and response times.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-green"
    iconWrapperClass="rounded-full bg-green/10 p-4 mb-6"
    :buttonHref="route('uptime.monitor.create')"
    :buttonText="__('Add Uptime Monitor')"
    buttonClass="bg-gradient-to-r from-green via-cyan to-green bg-300% hover:shadow-lg hover:shadow-green/30 transition-all duration-300"
/>

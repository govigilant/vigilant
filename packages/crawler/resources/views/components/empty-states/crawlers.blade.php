<x-frontend::empty-state
    :title="__('No Crawlers Configured')"
    :description="__('Add a crawler to monitor your site structure, discover issues, and keep content healthy.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-purple"
    iconWrapperClass="rounded-full bg-purple/10 p-4 mb-6"
    :buttonHref="route('crawler.create')"
    :buttonText="__('Add Crawler')"
    buttonClass="bg-purple hover:bg-purple/90 text-base-50 px-5 py-2.5 rounded-lg transition-all duration-300"
/>

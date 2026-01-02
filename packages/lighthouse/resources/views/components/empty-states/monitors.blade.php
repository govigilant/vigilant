<x-frontend::empty-state
    :title="__('No Lighthouse Monitors')"
    :description="__('Set up a Lighthouse monitor to track performance, accessibility, SEO, and best practices for your critical pages.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-orange"
    iconWrapperClass="rounded-full bg-orange/10 p-4 mb-6"
    :buttonHref="route('lighthouse.create')"
    :buttonText="__('Add Lighthouse Monitor')"
    buttonClass="bg-gradient-to-r from-orange via-yellow to-orange bg-300% hover:shadow-lg hover:shadow-orange/30 transition-all duration-300"
/>

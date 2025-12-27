<x-frontend::empty-state
    :title="__('No DNS Monitors Yet')"
    :description="__('Add your domains to keep track of DNS records, changes, and resolution issues.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-cyan"
    iconWrapperClass="rounded-full bg-cyan/10 p-4 mb-6"
    :buttonHref="route('dns.create')"
    :buttonText="__('Add DNS Monitor')"
    buttonClass="bg-cyan hover:bg-cyan/90 text-base-50 px-5 py-2.5 rounded-lg transition-all duration-300"
/>

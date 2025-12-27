<x-frontend::empty-state
    :title="__('No Certificate Monitors')"
    :description="__('Track TLS certificate expirations and changes by adding your first certificate monitor.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-teal"
    iconWrapperClass="rounded-full bg-teal/10 p-4 mb-6"
    :buttonHref="route('certificates.create')"
    :buttonText="__('Add Certificate Monitor')"
    buttonClass="bg-gradient-to-r from-teal via-cyan to-teal bg-300% text-base-50 px-5 py-2.5 rounded-lg hover:shadow-lg hover:shadow-teal/30 transition-all duration-300"
/>

<x-frontend::empty-state
    :title="__('No CVE Monitors')"
    :description="__('Add a CVE monitor to watch for newly disclosed vulnerabilities that match your keywords and severity thresholds.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-rose"
    iconWrapperClass="rounded-full bg-rose/10 p-4 mb-6"
    :buttonHref="route('cve.monitor.create')"
    :buttonText="__('Add CVE Monitor')"
    buttonClass="bg-rose hover:bg-rose/90 text-base-50 px-5 py-2.5 rounded-lg transition-all duration-300"
/>

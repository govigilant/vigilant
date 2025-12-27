@props(['site'])

<x-frontend::empty-state
    :title="__('No Monitors Configured')"
    :description="__('Get started by adding monitors for this site')"
    icon="tni-folder-plus-o"
    iconClass="h-12 w-12 text-red"
    iconWrapperClass="rounded-full bg-red/10 p-4 mb-6"
    :buttonHref="route('site.edit', ['site' => $site])"
    :buttonText="__('Configure Monitors')"
    buttonClass="bg-gradient-to-r from-red via-orange to-red bg-300% hover:shadow-lg hover:shadow-red/30 transition-all duration-300"
/>

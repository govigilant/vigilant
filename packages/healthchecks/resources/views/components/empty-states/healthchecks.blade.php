<x-frontend::empty-state :title="__('No Healthchecks Yet')" :description="__('Healthchecks help you monitor the health of your application.')" icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-blue" iconWrapperClass="rounded-full bg-blue/10 p-4 mb-6" :buttonHref="route('healthchecks.create')"
    :buttonText="__('Add Healthcheck')"
    buttonClass="bg-gradient-to-r from-blue via-indigo to-purple bg-300% hover:shadow-lg hover:shadow-blue/30 transition-all duration-300" />

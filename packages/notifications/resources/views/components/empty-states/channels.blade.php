<x-frontend::empty-state
    :title="__('No Notification Channels')"
    :description="__('Connect email, chat, or webhook channels to deliver alerts to your team.')"
    icon="phosphor-warning-circle"
    iconClass="h-12 w-12 text-sky"
    iconWrapperClass="rounded-full bg-sky/10 p-4 mb-6"
    :buttonHref="route('notifications.channel.create')"
    :buttonText="__('Add Channel')"
    buttonClass="bg-sky hover:bg-sky/90 text-base-50 px-5 py-2.5 rounded-lg transition-all duration-300"
/>

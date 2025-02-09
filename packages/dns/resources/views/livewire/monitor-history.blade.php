<div>
    <x-slot name="header">
        <x-page-header title="DNS History for {{ $monitor->type->name }} record: {{ $monitor->record }}"
            :back="route('dns.index')">
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-history-table :monitor="$monitor" />

</div>

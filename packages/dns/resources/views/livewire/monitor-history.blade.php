<div>
    <x-slot name="header">
        <x-page-header title="DNS History for {{ $monitor->type->name }} record: {{ $monitor->record }}"
            :back="route('dns.index')">
            <form action="{{ route('dns.delete', ['monitor' => $monitor]) }}" method="POST" wire:ignore
                onsubmit="return confirm('Are you sure you want to delete this monitor?');">
                @csrf
                @method('DELETE')
                <x-form.button class="bg-red hover:bg-red-light" type="submit">
                    @lang('Delete')
                </x-form.button>
            </form>
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-history-table :monitor="$monitor" />

</div>

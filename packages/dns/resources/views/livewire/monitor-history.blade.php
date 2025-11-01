<div x-data="{ showDeleteModal: false }">
    <x-slot name="header">
        <x-page-header title="DNS History for {{ $monitor->type->name }} record: {{ $monitor->record }}"
            :back="route('dns.index')">
            <x-form.button class="bg-red" type="button" @click="$dispatch('open-delete-modal')">
                @lang('Delete')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-history-table :monitor="$monitor" />

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
        <x-frontend::modal show="showDeleteModal">
            <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                @lang('Delete DNS Monitor')
            </x-frontend::modal.header>

            <x-frontend::modal.body>
                <div class="space-y-4">
                    <p class="text-base-100">
                        @lang('Are you sure you want to delete this DNS monitor?')
                    </p>
                    <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-base-300">
                                    <span class="font-semibold text-base-100">{{ $monitor->type->name }}</span> record for 
                                    <span class="font-semibold text-base-100">{{ $monitor->record }}</span>
                                </p>
                                <p class="text-sm text-base-400 mt-1">
                                    @lang('This action cannot be undone. All history for this monitor will be permanently deleted.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-frontend::modal.body>

            <x-frontend::modal.footer>
                <x-form.button type="button" @click="showDeleteModal = false">
                    @lang('Cancel')
                </x-form.button>
                <form action="{{ route('dns.delete', ['monitor' => $monitor]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-form.button class="bg-red" type="submit">
                        @lang('Delete Monitor')
                    </x-form.button>
                </form>
            </x-frontend::modal.footer>
        </x-frontend::modal>
    </div>
</div>

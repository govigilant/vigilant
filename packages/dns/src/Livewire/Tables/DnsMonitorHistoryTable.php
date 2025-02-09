<?php

namespace Vigilant\Dns\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Models\DnsMonitorHistory;
use Vigilant\Frontend\Integrations\Table\HoverColumn;

class DnsMonitorHistoryTable extends LivewireTable
{
    protected string $model = DnsMonitorHistory::class;

    public DnsMonitor $monitor;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    public function mount(DnsMonitor $monitor): void
    {
        $this->monitor = $monitor;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Type'), 'type')
                ->searchable()
                ->sortable(),

            HoverColumn::make(__('Value'), 'value')
                ->searchable()
                ->sortable(),

            Column::make(__('Modified At'), 'created_at')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()->where('dns_monitor_id', '=', $this->monitor->id);
    }
}

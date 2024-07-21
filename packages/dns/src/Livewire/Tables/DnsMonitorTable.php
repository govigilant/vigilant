<?php

namespace Vigilant\Dns\Livewire\Tables;

use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Integrations\Table\GeoIpColumn;

class DnsMonitorTable extends LivewireTable
{
    protected string $model = DnsMonitor::class;

    protected function columns(): array
    {
        return [
            Column::make(__('Type'), 'type')
                ->searchable()
                ->sortable(),

            Column::make(__('Record'), 'record')
                ->searchable()
                ->sortable(),

            Column::make(__('Value'), 'value')
                ->searchable()
                ->sortable(),

            GeoIpColumn::make(__('GeoIP'), 'geoip.country_code'),

        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => $monitor->delete());
            }),
        ];
    }
}

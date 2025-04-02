<?php

namespace Vigilant\Dns\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Dns\Jobs\CheckDnsRecordJob;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\GeoIpColumn;
use Vigilant\Frontend\Integrations\Table\HoverColumn;
use Vigilant\Frontend\Integrations\Table\StatusColumn;

class DnsMonitorTable extends LivewireTable
{
    protected string $model = DnsMonitor::class;

    protected function columns(): array
    {
        return [
            StatusColumn::make(__('Status'))
                ->text(function (DnsMonitor $monitor): string {
                    return $monitor->enabled ? __('Enabled') : __('Disabled');
                })
                ->status(function (DnsMonitor $monitor): Status {
                    return $monitor->enabled ? Status::Success : Status::Danger;
                }),

            Column::make(__('Type'), 'type')
                ->searchable()
                ->sortable(),

            Column::make(__('Record'), 'record')
                ->searchable()
                ->sortable(),

            HoverColumn::make(__('Value'), 'value')
                ->searchable()
                ->sortable(),

            Column::make(__('Last modified'), fn (DnsMonitor $monitor): string => $monitor->lastHistory()?->created_at?->toDateString() ?? '-'),

            GeoIpColumn::make(__('Location'), 'geoip.country_code'),
        ];
    }

    protected function link(Model $record): string
    {
        return route('dns.history', ['monitor' => $record]);
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Check'), 'check', function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => CheckDnsRecordJob::dispatch($monitor));
            }),

            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => $monitor->update(['enabled' => false]));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => $monitor->delete());
            }),
        ];
    }
}

<?php

namespace Vigilant\Dns\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use Vigilant\Dns\Jobs\CheckDnsRecordJob;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\GeoIpColumn;
use Vigilant\Frontend\Integrations\Table\HoverColumn;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Sites\Models\Site;

class DnsMonitorTable extends BaseTable
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

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Site'), 'site_id')
                ->options(
                    Site::query()
                        ->orderBy('url')
                        ->pluck('url', 'id')
                        ->toArray()
                ),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Check'), function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => CheckDnsRecordJob::dispatch($monitor));
            }, 'check'),

            Action::make(__('Enable'), function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }, 'enable'),

            Action::make(__('Disable'), function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => $monitor->update(['enabled' => false]));
            }, 'disable'),

            Action::make(__('Delete'), function (Enumerable $models): void {
                $models->each(fn (DnsMonitor $monitor) => $monitor->delete());
            }, 'delete'),
        ];
    }
}

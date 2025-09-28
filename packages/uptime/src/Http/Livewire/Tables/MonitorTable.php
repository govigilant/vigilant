<?php

namespace Vigilant\Uptime\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Frontend\Integrations\Table\ChartColumn;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Models\ResultAggregate;

class MonitorTable extends LivewireTable
{
    protected string $model = Monitor::class;

    protected array $pollingOptions = [
        '' => 'None',
        '30s' => 'Every 30 seconds',
    ];

    protected function columns(): array
    {
        /** @var CalculateUptimePercentage $calculateUptime */
        $calculateUptime = app(CalculateUptimePercentage::class);

        return [
            StatusColumn::make(__('Status'))
                ->text(function (Monitor $monitor): string {
                    if (! $monitor->enabled) {
                        return __('Disabled');
                    }

                    $downtime = $monitor->currentDowntime();

                    if ($downtime !== null) {
                        return __('Down');
                    }

                    /** @var null|Result|ResultAggregate $lastResult */
                    $lastResult = $monitor->results()->orderByDesc('created_at')->first();

                    if ($lastResult === null) {
                        $lastResult = $monitor->aggregatedResults()->orderByDesc('created_at')->first();
                    }

                    if ($lastResult === null || ! isset($lastResult->created_at)) {
                        return __('Unknown');
                    }

                    if ($lastResult->created_at->lessThan(now()->subMinutes(5))) {
                        return __('Last check: :time', ['time' => $lastResult->created_at->diffForHumans()]);
                    }

                    return __('Up');
                })
                ->status(function (Monitor $monitor): Status {
                    $downtime = $monitor->currentDowntime();

                    if ($downtime !== null || ! $monitor->enabled) {
                        return Status::Danger;
                    }

                    /** @var null|Result|ResultAggregate $lastResult */
                    $lastResult = $monitor->results()->orderByDesc('created_at')->first() ?? $monitor->aggregatedResults()->orderByDesc('created_at')->first();

                    if ($lastResult === null || $lastResult->created_at === null || $lastResult->created_at->lessThan(now()->subMinutes(5))) {
                        return Status::Warning;
                    }

                    return Status::Success;
                }),

            Column::make(__('Name'), 'name')
                ->searchable()
                ->sortable(),

            ChartColumn::make(__('Latency'))
                ->component('monitor-column-latency-chart')
                ->parameters(fn (Monitor $monitor) => ['monitorId' => $monitor->id]),

            Column::make(__('Uptime'))
                ->displayUsing(function (Monitor $monitor) use ($calculateUptime) {

                    $percentage = $calculateUptime->calculate($monitor);

                    if ($percentage === null) {
                        return __('Not available yet');
                    }

                    $class = match (true) {
                        $percentage > 95 => 'text-green-light',
                        $percentage > 80 => 'text-orange',
                        default => 'text-red'
                    };

                    return "<span class='$class'>$percentage%</span>";
                })
                ->asHtml(),

            Column::make(__('Last downtime'))
                ->displayUsing(function (Monitor $monitor) {
                    /** @var ?Downtime $lastDowntime */
                    $lastDowntime = $monitor->downtimes()
                        ->whereNotNull('end')
                        ->orderByDesc('start')
                        ->first();

                    if ($lastDowntime === null) {
                        return __('Never');
                    }

                    return teamTimezone($lastDowntime->start)->diffForHumans();
                }),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (Monitor $monitor) => $monitor->update(['enabled' => false]));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (Monitor $monitor) => $monitor->delete());
            }),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('uptime.monitor.view', ['monitor' => $model]);
    }
}

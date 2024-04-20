<?php

namespace Vigilant\Uptime\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
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

    protected function columns(): array
    {
        /** @var CalculateUptimePercentage $calculateUptime */
        $calculateUptime = app(CalculateUptimePercentage::class);

        return [

            StatusColumn::make(__('Status'))
                ->text(function (Monitor $monitor): string {
                    $downtime = $monitor->currentDowntime();

                    if ($downtime !== null) {
                        return __('Down');
                    }

                    /** @var null|Result|ResultAggregate $lastResult */
                    $lastResult = $monitor->results()->orderByDesc('created_at')->first();

                    if ($lastResult === null) {
                        $lastResult = $monitor->aggregatedResults()->orderByDesc('created_at')->first();
                    }

                    if ($lastResult === null) {
                        return __('Unknown');
                    }

                    if ($lastResult->created_at !== null && $lastResult->created_at->lessThan(now()->subMinutes(5))) {
                        return __('Last check: :time', ['time' => $lastResult->created_at->diffForHumans()]);
                    }

                    return __('Up');
                })
                ->status(function (Monitor $monitor): Status {
                    $downtime = $monitor->currentDowntime();

                    if ($downtime !== null) {
                        return Status::Danger;
                    }

                    /** @var null|Result|ResultAggregate $lastResult */
                    $lastResult = $monitor->results()->orderByDesc('created_at')->first() ?? $monitor->aggregatedResults()->orderByDesc('created_at')->first();

                    if ($lastResult === null || $lastResult->created_at === null || $lastResult->created_at->lessThan(now()->subMinutes(5))) {
                        return Status::Warning;
                    }

                    return Status::Success;
                }),

            Column::make(__('Name'), 'name'),

            ChartColumn::make(__('Latency'))
                ->component('monitor-latency-chart')
                ->parameters(fn (Monitor $monitor) => ['monitorId' => $monitor->id]),

            Column::make(__('Uptime'))
                ->displayUsing(function (Monitor $monitor) use ($calculateUptime) {

                    $percentage = $calculateUptime->calculate($monitor);

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

                    $duration = $lastDowntime->start->longAbsoluteDiffForHumans($lastDowntime->end);

                    return "{$lastDowntime->start->diffForHumans()} for $duration";

                }),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('uptime.monitor.edit', ['monitor' => $model]);
    }
}

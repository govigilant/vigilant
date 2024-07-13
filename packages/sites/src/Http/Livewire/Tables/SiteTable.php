<?php

namespace Vigilant\Sites\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseSitesTable;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;

class SiteTable extends LivewireTable
{
    protected string $model = Site::class;

    protected function columns(): array
    {
        /** @var CalculateUptimePercentage $calculateUptime */
        $calculateUptime = app(CalculateUptimePercentage::class);

        return [
            Column::make(__('URL'), 'url'),

            Column::make(__('Average Lighthouse Score'))
                ->displayUsing(function (Site $site) {

                    /** @var ?LighthouseMonitor $monitor */
                    $monitor = $site->lighthouseMonitors()->first();

                    if ($monitor === null) {
                        return __('Not Monitored');
                    }

                    /** @var ?LighthouseResult $result */
                    $result = $monitor->lighthouseResults()->orderByDesc('id')->first();

                    if ($result === null) {
                        return __('No Results');
                    }

                    $scores = [
                        $result->performance,
                        $result->accessibility,
                        $result->best_practices,
                        $result->seo,
                    ];

                    $score = array_sum($scores) / count($scores);

                    return LighthouseSitesTable::scoreDisplay($score);
                })
                ->asHtml(),

            Column::make(__('Uptime'))
                ->displayUsing(function (Site $site) use ($calculateUptime) {

                    /** @var ?Monitor $monitor */
                    $monitor = $site->uptimeMonitors()->first();

                    if ($monitor === null) {
                        return __('Not Monitored');
                    }

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
                ->displayUsing(function (Site $site) {
                    /** @var ?Monitor $monitor */
                    $monitor = $site->uptimeMonitors()->first();

                    if ($monitor === null) {
                        return __('Not Monitored');
                    }

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
            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (Site $site) => $site->delete());
            }),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('site.view', ['site' => $model]);
    }
}

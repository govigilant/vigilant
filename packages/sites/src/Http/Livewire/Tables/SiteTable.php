<?php

namespace Vigilant\Sites\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseMonitorsTable;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Downtime;

class SiteTable extends BaseTable
{
    protected string $model = Site::class;

    /**
     * @return array<int, \RamonRietdijk\LivewireTables\Columns\BaseColumn>
     */
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
                        return null;
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

                    return LighthouseMonitorsTable::scoreDisplay($score);
                })
                ->asHtml(),

            Column::make(__('Uptime'))
                ->displayUsing(function (Site $site) use ($calculateUptime) {

                    $monitor = $site->uptimeMonitor;

                    if ($monitor === null) {
                        return null;
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
                    $monitor = $site->uptimeMonitor;

                    if ($monitor === null) {
                        return null;
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

            StatusColumn::make(__('Link Issues'))
                ->text(function (Site $site) {
                    $crawler = $site->crawler;
                    if ($crawler === null) {
                        return null;
                    }

                    return __(':count issues', ['count' => $crawler->issueCount() ?? '0']);
                })
                ->status(function (Site $site): ?Status {
                    $crawler = $site->crawler;

                    if ($crawler === null) {
                        return null;
                    }

                    $count = $crawler->issueCount();

                    if ($count === null || $count === 0) {
                        return Status::Success;
                    }

                    $total = $crawler->totalUrlCount();

                    $threshold = $total * 0.05;

                    return $count > $threshold
                        ? Status::Danger
                        : Status::Warning;
                }),

            StatusColumn::make(__('Certificate'))
                ->text(function (Site $site) {
                    $certificate = $site->certificateMonitor;

                    if ($certificate === null || $certificate->valid_to === null) {
                        return null;
                    }

                    return __('Expires in :diff', [
                        'diff' => teamTimezone($certificate->valid_to)->longAbsoluteDiffForHumans(),
                    ]);
                })
                ->status(function (Site $site): ?Status {
                    $certificate = $site->certificateMonitor;

                    if ($certificate === null || $certificate->valid_to === null) {
                        return null;
                    }

                    $validTo = $certificate->valid_to;
                    $diff = now()->diffInDays($validTo);

                    if ($diff > 30) {
                        return Status::Success;
                    }

                    if ($diff > 7) {
                        return Status::Warning;
                    }

                    return Status::Danger;
                }),

        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Delete'), function (Enumerable $models): void {
                $models->each(fn (Site $site) => $site->delete());
            }, 'delete'),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('site.view', ['site' => $model]);
    }
}

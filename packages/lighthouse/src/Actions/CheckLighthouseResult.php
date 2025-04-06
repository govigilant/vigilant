<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Collection;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class CheckLighthouseResult
{
    protected array $categories = [
        'performance',
        'accessibility',
        'best_practices',
        'seo',
    ];

    public function __construct(protected CheckLighthouseResultAudit $checkLighthouseResultAudit) {}

    public function check(LighthouseResult $result): void
    {
        $totalResultCount = LighthouseResult::query()
            ->where('lighthouse_monitor_id', '=', $result->lighthouse_monitor_id)
            ->count();

        // Not enough data
        if ($totalResultCount < 10) {
            return;
        }

        // take 10% of the result set to calculate the current value
        $currentLimit = (int) floor($totalResultCount * 0.1);

        // take 30% of the result set before the current to calculate the previous value
        $previousLimit = (int) floor($totalResultCount * 0.3);

        $current = $this->averageResults($result->lighthouse_monitor_id, $currentLimit, 0)
            ->mapWithKeys(fn (?float $score, string $key) => [$key.'_new' => $score ?? 0]);

        $previous = $this->averageResults($result->lighthouse_monitor_id, $previousLimit, $currentLimit)
            ->mapWithKeys(fn (?float $score, string $key) => [$key.'_old' => $score ?? 0]);

        $data = CategoryResultDifferenceData::of($current->merge($previous)->toArray());

        CategoryScoreChangedNotification::notify($result, $data);

        /** @var Collection<int, LighthouseResultAudit> $audits */
        $audits = $result->audits()->get();

        $audits->each(fn (LighthouseResultAudit $audit) => $this->checkLighthouseResultAudit->check($audit));
    }

    protected function averageResults(int $lighthouseSiteId, int $count, int $skip): Collection
    {
        $results = LighthouseResult::query()
            ->where('lighthouse_monitor_id', '=', $lighthouseSiteId)
            ->orderByDesc('id')
            ->skip($skip)
            ->take($count)
            ->get();

        return collect($this->categories)
            ->mapWithKeys(fn (string $category): array => [$category => $results->average($category)]);
    }
}

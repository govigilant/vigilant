<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Collection;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class CheckLighthouseResult
{
    protected const CURRENT_WINDOW = 10;

    protected const PREVIOUS_WINDOW = 10;

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

        // Not enough data to fill both windows.
        if ($totalResultCount < self::CURRENT_WINDOW + self::PREVIOUS_WINDOW) {
            return;
        }

        $current = $this->averageResults($result->lighthouse_monitor_id, self::CURRENT_WINDOW, 0)
            ->mapWithKeys(fn (?float $score, string $key) => [$key.'_new' => $score ?? 0]);

        $previous = $this->averageResults($result->lighthouse_monitor_id, self::PREVIOUS_WINDOW, self::CURRENT_WINDOW)
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

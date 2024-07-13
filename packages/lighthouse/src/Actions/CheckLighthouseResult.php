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

    public function __construct(protected CheckLighthouseResultAudit $checkLighthouseResultAudit)
    {
    }

    public function check(LighthouseResult $result): void
    {
        $current = $this->averageResults($result->lighthouse_monitor_id, 4, 0)
            ->mapWithKeys(fn (?float $score, string $key) => [$key.'_new' => $score ?? 0]);

        $previous = $this->averageResults($result->lighthouse_monitor_id, 12, 4)
            ->mapWithKeys(fn (?float $score, string $key) => [$key.'_old' => $score ?? 0]);

        $data = CategoryResultDifferenceData::of($current->merge($previous)->toArray());

        CategoryScoreChangedNotification::notify($result, $data);

        $result->audits()
            ->get()
            ->each(fn (LighthouseResultAudit $audit) => $this->checkLighthouseResultAudit->check($audit));
    }

    protected function averageResults(int $lighthouseSiteId, int $count = 3, int $skip = 0): Collection
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

<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Collection;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class CheckLighthouseResult
{
    protected array $categories = [
        'performance',
        'accessibility',
        'best_practices',
        'seo',
    ];

    public function check(LighthouseResult $result): void
    {
        $current = $this->averageResults($result->lighthouse_site_id, 2, 0)
            ->mapWithKeys(fn (float $score, string $key) => [$key.'_new' => $score]);

        $previous = $this->averageResults($result->lighthouse_site_id, 2, 2)
            ->mapWithKeys(fn (float $score, string $key) => [$key.'_old' => $score]);

        $data = CategoryResultDifferenceData::of($current->merge($previous)->toArray());

        // Ignore changes of less than 3%
        $shouldNotify = abs($data->averageDifference()) > 3;

        if ($shouldNotify) {
            CategoryScoreChangedNotification::notify($result, $data);
        }
    }

    protected function averageResults(int $lighthouseSiteId, int $count = 3, int $skip = 0): Collection
    {
        $results = LighthouseResult::query()
            ->where('lighthouse_site_id', '=', $lighthouseSiteId)
            ->orderByDesc('id')
            ->skip($skip)
            ->take($count)
            ->get();

        return collect($this->categories)
            ->mapWithKeys(fn (string $category): array => [$category => $results->average($category)]);
    }
}

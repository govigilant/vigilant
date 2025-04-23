<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Arr;
use Vigilant\Cve\Jobs\MatchCveMonitorsJob;
use Vigilant\Cve\Models\Cve;

class ImportCve
{
    public function import(array $cve, bool $notify = true): void
    {
        /** @var array<int, array<string, string>> $descriptions */
        $descriptions = data_get($cve, 'cve.descriptions', []);

        $description = collect($descriptions)->firstWhere('lang', '=', 'en');

        if ($description === null) {
            $description = Arr::first($descriptions);
        }

        $score = data_get($cve, 'cve.metrics.cvssMetricV31.0.cvssData.baseScore');

        if ($score === null) {
            $score = data_get($cve, 'cve.metrics.cvssMetricV2.0.cvssData.baseScore');
        }

        $model = Cve::query()->updateOrCreate([
            'identifier' => data_get($cve, 'cve.id'),
        ], [
            'score' => $score,
            'description' => $description['value'] ?? '',
            'published_at' => data_get($cve, 'cve.published'),
            'modified_at' => data_get($cve, 'cve.lastModified'),
            'data' => $cve,
        ]);

        if ($notify && $model->wasRecentlyCreated && $model->published_at->isAfter(now()->subWeek())) {
            MatchCveMonitorsJob::dispatch($model);
        }
    }
}

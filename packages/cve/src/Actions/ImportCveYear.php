<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Vigilant\Cve\Jobs\MatchCveMonitorsJob;
use Vigilant\Cve\Models\Cve;

class ImportCveYear
{
    public function import(int $year): void
    {
        $endpoint = "https://nvd.nist.gov/feeds/json/cve/1.1/nvdcve-1.1-{$year}.json.gz";

        $response = Http::get($endpoint)
            ->throw()
            ->body();

        $response = json_decode(gzdecode($response), true);

        $cves = $response['CVE_Items'] ?? [];

        foreach ($cves as $cve) {

            /** @var array<int, array<string, string>> $descriptions */
            $descriptions = data_get($cve, 'cve.description.description_data', []);

            $description = collect($descriptions)->firstWhere('lang', '=', 'en');

            if ($description === null) {
                $description = Arr::first($descriptions);
            }

            $score = data_get($cve, 'impact.baseMetricV3.cvssV3.baseScore');

            if ($score === null) {
                $score = data_get($cve, 'impact.baseMetricV2.cvssV2.baseScore');
            }

            $model = Cve::query()->updateOrCreate([
                'identifier' => data_get($cve, 'cve.CVE_data_meta.ID'),
            ], [
                'score' => $score,
                'description' => $description['value'] ?? '',
                'published_at' => data_get($cve, 'publishedDate'),
                'modified_at' => data_get($cve, 'lastModifiedDate'),
                'data' => $cve,
            ]);

            if ($model->wasRecentlyCreated && $model->published_at->isAfter(now()->subWeek())) {
                MatchCveMonitorsJob::dispatch($model);
            }
        }
    }
}

<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Vigilant\Cve\Jobs\MatchCveMonitorsJob;
use Vigilant\Cve\Models\Cve;

class ImportCves
{
    public function import(?Carbon $from = null): void
    {
        if ($from === null) {
            $from = Cve::query()
                ->orderBy('published_at', 'desc')
                ->first()->published_at ?? null;
        }
        if ($from === null) {
            $from = now()->subDay();
        }

        $endpoint = 'https://services.nvd.nist.gov/rest/json/cves/2.0';

        $to = $from->clone();
        $to->addDays(30);

        if ($to->isFuture()) {
            $to = now();
        }

        $response = Http::get($endpoint, [
            'lastModStartDate' => $from->format('Y-m-d\TH:i:s\Z'),
            'lastModEndDate' => $to->format('Y-m-d\TH:i:s\Z'),
        ])->throw();

        $cves = $response->json('vulnerabilities', []);

        foreach ($cves as $cve) {

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

            if ($model->wasRecentlyCreated && $model->published_at->isAfter(now()->subWeek())) {
                MatchCveMonitorsJob::dispatch($model);
            }
        }
    }
}

<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Facades\Process;
use Vigilant\Lighthouse\Jobs\AggregateLighthouseBatchJob;
use Vigilant\Lighthouse\Jobs\LighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;

class Lighthouse
{
    public function __construct(protected CheckLighthouseResult $lighthouseResult) {}

    public function run(LighthouseMonitor $monitor, ?string $batchId): void
    {
        if ($batchId === null) {
            $batchId = str()->uuid();

            $monitor->update([
                'next_run' => now()->addMinutes($monitor->interval),
            ]);
        }

        $process = Process::run('lighthouse '.$monitor->url.' --output json --quiet --chrome-flags="--headless --no-sandbox --disable-dev-shm-usage --disable-gpu"')->throw();

        $result = json_decode($process->output(), true);

        /** @var array<string, array> $categoriesResult */
        $categoriesResult = $result['categories'] ?? [];

        /** @var array<string, array> $audits */
        $audits = $result['audits'];

        $categories = collect($categoriesResult)
            ->mapWithKeys(function (array $result, string $key): array {
                return [str_replace('-', '_', $key) => $result['score']];
            })
            ->toArray();

        /** @var LighthouseResult $result */
        $result = $monitor->lighthouseResults()->create(array_merge(['batch_id' => $batchId], $categories));

        foreach ($audits as $audit) {
            $result->audits()->create([
                'audit' => $audit['id'],
                'title' => $audit['title'],
                'explanation' => $audit['explanation'] ?? null,
                'description' => $audit['description'] ?? null,
                'score' => $audit['score'] ?? null,
                'scoreDisplayMode' => $audit['scoreDisplayMode'],
                'details' => $audit['details'] ?? null,
                'warnings' => $audit['warnings'] ?? null,
                'items' => $audit['items'] ?? null,
                'metricSavings' => $audit['metricSavings'] ?? null,
                'guidanceLevel' => $audit['guidanceLevel'] ?? null,
                'numericValue' => $audit['numericValue'] ?? null,
                'numericUnit' => $audit['numericUnit'] ?? null,
            ]);
        }

        $batchCount = $monitor->lighthouseResults()
            ->where('batch_id', $batchId)
            ->count();

        /** @var int $lighthouseRuns */
        $lighthouseRuns = config('lighthouse.runs');

        if ($batchCount >= $lighthouseRuns) {
            AggregateLighthouseBatchJob::dispatch($monitor, $batchId);
        } else {
            LighthouseJob::dispatch($monitor, $batchId);
        }
    }
}

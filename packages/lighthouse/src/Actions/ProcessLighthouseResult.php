<?php

namespace Vigilant\Lighthouse\Actions;

use Vigilant\Lighthouse\Jobs\AggregateLighthouseBatchJob;
use Vigilant\Lighthouse\Jobs\RunLighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;

class ProcessLighthouseResult
{
    public function process(LighthouseMonitor $monitor, string $batchId, array $result): void
    {
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
            RunLighthouseJob::dispatch($monitor, $batchId);
        }
    }
}

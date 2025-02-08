<?php

namespace Vigilant\Lighthouse\Actions;

use Vigilant\Lighthouse\Jobs\CheckLighthouseResultJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;

class AggregateLighthouseBatch
{
    public function aggregateBatch(LighthouseMonitor $monitor, string $batchId): void
    {
        $results = $monitor->lighthouseResults()->where('batch_id', '=', $batchId)->get();

        if ($results->isEmpty()) {
            return;
        }

        if ($results->count() === 1) {
            CheckLighthouseResultJob::dispatch($results->first());

            return;
        }

        /** @var LighthouseResult $resultAverages */
        $resultAverages = $monitor->lighthouseResults()
            ->where('batch_id', '=', $batchId)
            ->groupBy('batch_id')
            ->selectRaw('AVG(performance) as performance, AVG(accessibility) as accessibility, AVG(best_practices) as best_practices, AVG(seo) as seo')
            ->first();

        /** @var LighthouseResult $newResult */
        $newResult = $monitor->lighthouseResults()->create([
            'performance' => $resultAverages->performance,
            'accessibility' => $resultAverages->accessibility,
            'best_practices' => $resultAverages->best_practices,
            'seo' => $resultAverages->seo,
        ]);

        /** @var LighthouseResult $firstResult */
        $firstResult = $monitor->lighthouseResults()->where('batch_id', '=', $batchId)->first();

        $auditCategories = $firstResult->audits()->select('audit')->distinct()->get()->pluck('audit')->toArray();

        foreach ($auditCategories as $audit) {

            /** @var ?LighthouseResultAudit $averages */
            $averages = LighthouseResultAudit::query()
                ->whereIn('lighthouse_result_id', $results->pluck('id'))
                ->where('audit', '=', $audit)
                ->selectRaw('AVG(score) as score, AVG(numericValue) as numericValue')
                ->first();

            /** @var ?LighthouseResultAudit $allValues */
            $allValues = LighthouseResultAudit::query()
                ->whereIn('lighthouse_result_id', $results->pluck('id'))
                ->where('audit', '=', $audit)
                ->first();

            if ($averages === null || $allValues === null) {
                continue;
            }

            $newResult->audits()->create(array_merge([
                'audit' => $audit,
                'score' => $averages->score,
                'numericValue' => $averages->numericValue,
            ], $allValues->only(['title', 'explanation', 'description', 'scoreDisplayMode', 'details', 'warnings', 'items', 'metricSavings', 'guidanceLevel', 'numericUnit'])));
        }

        $monitor->lighthouseResults()->where('batch_id', '=', $batchId)->delete();

        CheckLighthouseResultJob::dispatch($newResult);
    }
}

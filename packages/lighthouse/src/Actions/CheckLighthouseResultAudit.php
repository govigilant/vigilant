<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Database\Query\JoinClause;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;

class CheckLighthouseResultAudit
{
    public function check(LighthouseResultAudit $audit): void
    {
        if ($audit->scoreDisplayMode !== 'numeric') {
            return;
        }

        /** @var ?int $monitorId */
        $monitorId = $audit->lighthouseResult?->lighthouse_monitor_id;

        throw_if($monitorId === null, 'Invalid relationship');

        $totalResultCount = LighthouseResultAudit::query()
            ->join('lighthouse_results', function (JoinClause $join) use ($monitorId) {
                $join->on('lighthouse_results.id', '=', 'lighthouse_result_audits.lighthouse_result_id')
                    ->where('lighthouse_results.lighthouse_monitor_id', '=', $monitorId)
                    ->where('lighthouse_results.created_at', '>', now()->subMonth());
            })
            ->count();

        // take 10% of the result set to calculate the current value
        $currentLimit = (int) floor($totalResultCount * 0.1);

        // take 20% of the result set before the current to calculate the previous value
        $previousLimit = (int) floor($totalResultCount * 0.3);

        $current = $this->averageNumericValue($audit, $currentLimit, 0);

        $previous = $this->averageNumericValue($audit, $previousLimit, $currentLimit);

        if ($previous == 0) {
            $percentDifference = ($current == 0) ? 0 : 100;
        } else {
            $percentDifference = (($current - $previous) / $previous) * 100;
        }

        if ($percentDifference > 0) {
            NumericAuditChangedNotification::notify($audit, $percentDifference, $previous, $current);
        }
    }

    protected function averageNumericValue(LighthouseResultAudit $audit, int $count = 3, int $skip = 0): float
    {
        /** @var ?int $monitorId */
        $monitorId = $audit->lighthouseResult?->lighthouse_monitor_id;

        throw_if($monitorId === null, 'Invalid relationship');

        return (float) LighthouseResultAudit::query()
            ->join('lighthouse_results', function (JoinClause $join) use ($monitorId) {
                $join->on('lighthouse_results.id', '=', 'lighthouse_result_audits.lighthouse_result_id')
                    ->where('lighthouse_results.lighthouse_monitor_id', '=', $monitorId);
            })
            ->where('audit', '=', $audit->audit)
            ->orderByDesc('lighthouse_result_audits.id')
            ->skip($skip)
            ->take($count)
            ->get()
            ->average('numericValue');
    }
}

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
        $current = $this->averageNumericValue($audit, 10, 0);

        $previous = $this->averageNumericValue($audit, 50, 10);

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
        return (float) LighthouseResultAudit::query()
            ->join('lighthouse_results', function (JoinClause $join) use ($audit) {
                $join->on('lighthouse_results.id', '=', 'lighthouse_result_audits.lighthouse_result_id')
                    ->where('lighthouse_result.lighthouse_monitor_id', '=',
                        $audit->lighthouseResult->lighthouse_monitor_id);
            })
            ->where('audit', '=', $audit->audit)
            ->orderByDesc('lighthouse_result_audits.id')
            ->skip($skip)
            ->take($count)
            ->get()
            ->average('numericValue');
    }
}

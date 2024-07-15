<?php

namespace Vigilant\Lighthouse\Actions;

use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;

class CheckLighthouseResultAudit
{
    public function check(LighthouseResultAudit $audit): void
    {
        if ($audit->scoreDisplayMode !== 'numeric') {
            return;
        }

        $current = $this->averageNumericValue($audit, 4, 0);

        $previous = $this->averageNumericValue($audit, 12, 4);

        if ($previous == 0) {
            $percentDifference = ($current == 0) ? 0 : 100;
        } else {
            $percentDifference = (($previous - $current) / $previous) * 100;
        }

        if ($percentDifference > 0) {
            NumericAuditChangedNotification::notify($audit, $percentDifference);
        }
    }

    protected function averageNumericValue(LighthouseResultAudit $audit, int $count = 3, int $skip = 0): float
    {
        return (float) LighthouseResultAudit::query()
            ->where('lighthouse_result_id', '=', $audit->lighthouse_result_id)
            ->where('audit', '=', $audit->audit)
            ->orderByDesc('id')
            ->skip($skip)
            ->take($count)
            ->get()
            ->average('numericValue');
    }
}

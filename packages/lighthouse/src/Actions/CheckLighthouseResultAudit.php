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
            $percentDifference = (($current - $previous) / $previous) * 100;
        }

        // Ignore changes of less than 3%
        $shouldNotify = $percentDifference > 3 || $percentDifference < -3;

        if ($shouldNotify) {
            NumericAuditChangedNotification::notify($audit, $percentDifference);
        }
    }

    protected function averageNumericValue(LighthouseResultAudit $audit, int $count = 3, int $skip = 0): float
    {
        return LighthouseResultAudit::query()
            ->where('lighthouse_result_id', '=', $audit->lighthouse_result_id)
            ->orderByDesc('id')
            ->skip($skip)
            ->take($count)
            ->average('numericValue');
    }
}

<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Category;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class PerformancePercentScoreCondition extends ScoreCondition
{
    public static string $name = 'Performance score change in percent';

    protected function score(CategoryScoreChangedNotification $notification): float
    {
        return $notification->data->performanceDifference();
    }
}

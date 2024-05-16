<?php

namespace Vigilant\Lighthouse\Notifications\Conditions;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class AccessibilityPercentScoreCondition extends ScoreCondition
{
    public static string $name = 'Accessibility score change in percent';

    protected function score(CategoryScoreChangedNotification $notification): float
    {
        return $notification->data->accessibilityDifference();
    }
}

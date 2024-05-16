<?php

namespace Vigilant\Lighthouse\Notifications\Conditions;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class BestPracticesPercentScoreCondition extends ScoreCondition
{
    public static string $name = 'Best practices score change in percent';

    protected function score(CategoryScoreChangedNotification $notification): float
    {
        return $notification->data->bestPracticesDifference();
    }
}

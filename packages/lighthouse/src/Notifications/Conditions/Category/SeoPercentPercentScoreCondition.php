<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Category;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;

class SeoPercentPercentScoreCondition extends ScoreCondition
{
    public static string $name = 'SEO score change in percent';

    protected function score(CategoryScoreChangedNotification $notification): float
    {
        return $notification->data->seoDifference();
    }
}

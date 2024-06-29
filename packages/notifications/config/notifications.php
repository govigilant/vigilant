<?php

return [
    /*
     * Move the old condition classes in the DB to the new ones
     * Old class => new class
     */
    'moved_conditions' => [
        \Vigilant\Lighthouse\Notifications\Conditions\AccessibilityPercentScoreCondition::class => \Vigilant\Lighthouse\Notifications\Conditions\Category\AccessibilityPercentScoreCondition::class,
        \Vigilant\Lighthouse\Notifications\Conditions\AverageScoreCondition::class => \Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreCondition::class,
        \Vigilant\Lighthouse\Notifications\Conditions\BestPracticesPercentScoreCondition::class => \Vigilant\Lighthouse\Notifications\Conditions\Category\BestPracticesPercentScoreCondition::class,
        \Vigilant\Lighthouse\Notifications\Conditions\SeoPercentPercentScoreCondition::class => \Vigilant\Lighthouse\Notifications\Conditions\Category\SeoPercentPercentScoreCondition::class,
    ],
];

<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Category;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class AverageScoreValueCondition extends Condition
{
    public static string $name = 'Average score value';

    public function operands(): array
    {
        return [
            'new' => 'New value',
            'old' => 'Old value',
        ];
    }

    public function operators(): array
    {
        return [
            '=' => 'Equal to',
            '<>' => 'Not equal to',
            '<' => 'Less than',
            '<=' => 'Less or equal than',
            '>' => 'Greater than',
            '>=' => 'Greater or equal than',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var CategoryScoreChangedNotification $notification */
        $scores = $operand === 'old'
            ? [
                $notification->data->performanceOld(),
                $notification->data->accessibilityOld(),
                $notification->data->bestPracticesOld(),
                $notification->data->seoOld(),
            ]
            : [
                $notification->data->performanceNew(),
                $notification->data->accessibilityNew(),
                $notification->data->bestPracticesNew(),
                $notification->data->seoNew(),
            ];

        $averageScore = collect($scores)->average() * 100;

        return match ($operator) {
            '=' => $averageScore == $value,
            '<>' => $averageScore != $value,
            '<' => $averageScore < $value,
            '<=' => $averageScore <= $value,
            '>' => $averageScore > $value,
            '>=' => $averageScore >= $value,
            default => false,
        };
    }
}

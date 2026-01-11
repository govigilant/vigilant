<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Category;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class PerformanceScoreValueCondition extends Condition
{
    public static string $name = 'Performance score value';

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
        $score = $operand === 'old'
            ? $notification->data->performanceOld() * 100
            : $notification->data->performanceNew() * 100;

        return match ($operator) {
            '=' => $score == $value,
            '<>' => $score != $value,
            '<' => $score < $value,
            '<=' => $score <= $value,
            '>' => $score > $value,
            '>=' => $score >= $value,
            default => false,
        };
    }
}

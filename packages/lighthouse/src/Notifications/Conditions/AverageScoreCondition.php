<?php

namespace Vigilant\Lighthouse\Notifications\Conditions;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class AverageScoreCondition extends Condition
{
    public static string $name = 'Average Score Change';

    public function operands(): array
    {
        return [
            'relative' => 'Relative',
            'absolute' => 'Absolute',
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

    /** @var CategoryScoreChangedNotification $notification */
    public function applies(
        Notification $notification,
        ?string $operand,
        string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        $averageScore = $notification->data->averageDifference();

        if ($operand === 'absolute') {
            $averageScore = abs($averageScore);
        }

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

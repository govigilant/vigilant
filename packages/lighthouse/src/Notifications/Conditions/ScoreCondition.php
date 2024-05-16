<?php

namespace Vigilant\Lighthouse\Notifications\Conditions;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

abstract class ScoreCondition extends Condition
{
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

    public function applies(
        Notification $notification,
        ?string $operand,
        string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var CategoryScoreChangedNotification $notification */
        $score = $this->score($notification);

        if ($operand === 'absolute') {
            $score = abs($score);
        }

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

    abstract protected function score(CategoryScoreChangedNotification $notification): float;
}

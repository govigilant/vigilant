<?php

namespace Vigilant\Cve\Notifications\Conditions;

use Vigilant\Cve\Notifications\CveMatchedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class ScoreCondition extends SelectCondition
{
    public static string $name = 'Score';

    public function options(): array
    {
        return range(0, 10);
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
        /** @var CveMatchedNotification $notification */
        $score = $notification->cve->score ?? 0;

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

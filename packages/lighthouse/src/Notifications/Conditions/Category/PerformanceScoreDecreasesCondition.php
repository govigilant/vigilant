<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Category;

use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class PerformanceScoreDecreasesCondition extends Condition
{
    public static string $name = 'Performance score decreases';

    public function operands(): array
    {
        return [];
    }

    public function operators(): array
    {
        return [
            '>=' => 'By at least',
            '>' => 'By more than',
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
        $change = $notification->data->performanceDifference();

        return match ($operator) {
            '>' => $change < -$value,
            '>=' => $change <= -$value,
            default => false,
        };
    }
}

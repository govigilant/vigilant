<?php

namespace Vigilant\Cve\Notifications\Conditions;

use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Notifications\CveMatchedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class KeywordCondition extends SelectCondition
{
    public static string $name = 'Keyword';

    public function options(): array
    {
        return CveMonitor::query()
            ->pluck('keyword', 'id')
            ->unique()
            ->toArray();
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var CveMatchedNotification $notification */

        return $notification->monitor->id === $value;
    }
}

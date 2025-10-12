<?php

namespace Vigilant\Uptime\Notifications\Conditions;

use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Models\Outpost;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;
use Vigilant\Uptime\Notifications\LatencyPeakNotification;

class CountryCondition extends SelectCondition
{
    public static string $name = 'Country';

    public function options(): array
    {
        return Outpost::query()
            ->whereNotNull('country')
            ->distinct('country')
            ->orderBy('country')
            ->pluck('country', 'country')
            ->toArray();
    }

    public function operators(): array
    {
        return [
            '=' => 'is',
            '!=' => 'is not',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var LatencyChangedNotification|LatencyPeakNotification $notification */
        $country = $notification->country;

        if ($country === null) {
            return false;
        }

        return match ($operator) {
            '=' => $country == $value,
            '!=' => $country != $value,
            default => false,
        };
    }
}

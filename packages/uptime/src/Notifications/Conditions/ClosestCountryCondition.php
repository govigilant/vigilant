<?php

namespace Vigilant\Uptime\Notifications\Conditions;

use Vigilant\Notifications\Conditions\StaticCondition;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;
use Vigilant\Uptime\Notifications\LatencyPeakNotification;

class ClosestCountryCondition extends StaticCondition
{
    public static string $name = 'Only from closest country';

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

        $closestCountry = $notification->monitor->closestOutpost?->country;

        if ($closestCountry === null) {
            return false;
        }

        return $country === $closestCountry;
    }

    public static function info(): ?string
    {
        return __('Only triggers when the notification originates from your geographically closest monitoring location.');
    }
}

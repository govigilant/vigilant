<?php

namespace Vigilant\Sites\Conditions;

use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class SiteCondition extends SelectCondition
{
    public static string $name = 'Site';

    public function applies(
        Notification $notification,
        ?string $operand,
        string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        if (! is_a($notification, HasSite::class)) {
            return false;
        }

        $site = $notification->site();

        if ($site === null) {
            return false;
        }

        return match ($operator) {
            '=' => $site->id == $value,
            '!=' => $site->id != $value,
            default => false,
        };
    }

    public function operators(): array
    {
        return [
            '=' => 'is',
            '!=' => 'is not',
        ];
    }

    public function options(): array
    {
        return Site::query()->get()
            ->mapWithKeys(fn (Site $site): array => [$site->id => $site->url])
            ->toArray();
    }
}

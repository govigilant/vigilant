<?php

namespace Vigilant\Dns\Notifications\Conditions;

use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Notifications\RecordChangedNotification;
use Vigilant\Dns\Notifications\RecordNotResolvedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class RecordTypeCondition extends SelectCondition
{
    public static string $name = 'Record Type';

    public function options(): array
    {
        return collect(Type::cases())
            ->mapWithKeys(fn (Type $type): array => [$type->value => $type->name])
            ->toArray();
    }

    public function operators(): array
    {
        return [
            '=' => 'is',
            '!=' => 'is not',
        ];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var RecordChangedNotification|RecordNotResolvedNotification $notification */
        $selectedType = Type::tryFrom($value);

        if ($selectedType === null) {
            return false;
        }

        $type = $notification->monitor->type;

        return match ($operator) {
            '=' => $type == $selectedType,
            '!=' => $type != $selectedType,
            default => false,
        };
    }
}

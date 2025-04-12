<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Audit;

use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class AuditTypeCondition extends SelectCondition
{
    public static string $name = 'Audit Type';

    public function operators(): array
    {
        return [
            '=' => 'Equal to',
            '<>' => 'Not equal to',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var NumericAuditChangedNotification $notification */
        $audit = $notification->audit->audit;

        return match ($operator) {
            '=' => $audit == $value,
            '<>' => $audit != $value,
            default => false,
        };
    }

    public function options(): array
    {
        return cache()->remember(
            'audit-type-condition:options',
            now()->addDay(),
            function (): array {
                return LighthouseResultAudit::query()
                    ->whereNotNull('numericValue')
                    ->select('audit', 'title', 'numericUnit')
                    ->distinct()
                    ->get()
                    ->mapWithKeys(fn (LighthouseResultAudit $audit) => [$audit->audit => $audit->title.' ('.$audit->numericUnit.')'])
                    ->toArray();
            }
        );
    }
}

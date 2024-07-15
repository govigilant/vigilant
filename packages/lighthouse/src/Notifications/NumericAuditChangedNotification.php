<?php

namespace Vigilant\Lighthouse\Notifications;

use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditPercentCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class NumericAuditChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Lighthouse numeric audit changed';

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => AuditPercentCondition::class,
                'operator' => '>=',
                'operand' => 'absolute',
                'value' => 10,
            ],
        ],
    ];

    public function __construct(
        public LighthouseResultAudit $audit,
        public float $percentChanged,
    ) {}

    public function title(): string
    {
        return __('Lighthouse numeric audit changed by :percent %', [
            'percent' => round($this->percentChanged),
        ]);
    }

    public function site(): ?Site
    {
        return $this->audit->lighthouseResult?->lighthouseSite?->site;
    }

    public function uniqueId(): string|int
    {
        return $this->audit->id;
    }
}

<?php

namespace Vigilant\Lighthouse\Notifications;

use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditPercentCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class NumericAuditChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Lighthouse numeric audit changed';

    public static bool $autoCreate = false;

    public Level $level = Level::Info;

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
        public float $previous,
        public float $current,
    ) {}

    public function title(): string
    {
        return __('Lighthouse numeric audit \':audit\' on :url changed by :percent %', [
            'audit' => $this->audit->title,
            'url' => $this->audit->lighthouseResult->lighthouseSite->url ?? '?',
            'percent' => round($this->percentChanged),
        ]);
    }

    public function description(): string
    {
        return __('Raw value changed from from :previous to :current', [
            'previous' => $this->roundRawValue($this->previous),
            'current' => $this->roundRawValue($this->current),
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

    protected function roundRawValue(float $rawValue): float
    {
        if ($rawValue > 10) {
            return round($rawValue);
        }

        if ($rawValue > 0) {
            return round($rawValue, 2);
        }

        if ($rawValue == 0) {
            return 0.00;
        }

        $strNumber = rtrim(rtrim(sprintf('%.10f', $rawValue), '0'), '.');

        return (float) $strNumber;
    }
}

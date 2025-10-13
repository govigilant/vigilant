<?php

namespace Vigilant\Certificates\Notifications;

use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Certificates\Notifications\Conditions\DaysCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CertificateExpiresInDaysNotification extends Notification implements HasSite
{
    public static string $name = 'Certificate Expires In Days';

    public Level $level = Level::Warning;

    public static ?int $defaultCooldown = 60 * 24;

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => DaysCondition::class,
                'value' => 14,
            ],
        ],
    ];

    public function __construct(public CertificateMonitor $monitor) {}

    public function title(): string
    {
        return __('The certificate on :domain will expire in :difference', [
            'domain' => $this->monitor->domain,
            'difference' => $this->monitor->valid_to?->shortAbsoluteDiffForHumans() ?? '?',
        ]);
    }

    public function description(): string
    {
        return __('The certificate on :domain will expire on :date which is :difference', [
            'domain' => $this->monitor->domain,
            'validto' => $this->monitor->valid_to?->shortAbsoluteDiffForHumans() ?? '?',
            'date' => $this->monitor->valid_to?->toDateString() ?? '?',
        ]);
    }

    public static function info(): ?string
    {
        return __('Triggered when an SSL certificate is approaching expiration.');
    }

    public function uniqueId(): string|int
    {
        return $this->monitor->id;
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}

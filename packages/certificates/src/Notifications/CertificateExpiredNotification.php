<?php

namespace Vigilant\Certificates\Notifications;

use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CertificateExpiredNotification extends Notification implements HasSite
{
    public static string $name = 'Certificate Expired';

    public Level $level = Level::Critical;

    public static ?int $defaultCooldown = 60 * 24 * 7;

    public function __construct(public CertificateMonitor $monitor) {}

    public function title(): string
    {
        return __('The certificate on :domain expired on :validTo', [
            'domain' => $this->monitor->domain,
            'validto' => $this->monitor->valid_to?->toDateString() ?? '?',
        ]);
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

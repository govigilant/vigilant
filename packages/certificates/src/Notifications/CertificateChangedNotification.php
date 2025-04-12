<?php

namespace Vigilant\Certificates\Notifications;

use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Certificates\Models\CertificateMonitorHistory;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CertificateChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Certificate Changed';

    public Level $level = Level::Info;

    public function __construct(public CertificateMonitor $monitor, public CertificateMonitorHistory $old) {}

    public function title(): string
    {
        return __('The certificate on :domain has been updated', [
            'domain' => $this->monitor->domain,
        ]);
    }

    public function description(): string
    {
        return __('The certificate on :domain has been updated. The new expiration date is :validto, old expiration date was :oldvalidto', [
            'domain' => $this->monitor->domain,
            'validto' => $this->monitor->valid_to?->toDateString() ?? '?',
            'oldvalidto' => $this->old->valid_to?->toDateString() ?? '?',
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

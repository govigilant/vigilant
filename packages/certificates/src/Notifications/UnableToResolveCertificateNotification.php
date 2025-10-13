<?php

namespace Vigilant\Certificates\Notifications;

use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class UnableToResolveCertificateNotification extends Notification implements HasSite
{
    public static string $name = 'Unable to resolve certificate';

    public Level $level = Level::Info;

    public static ?int $defaultCooldown = 60 * 24;

    public function __construct(public CertificateMonitor $monitor, public string $error) {}

    public function title(): string
    {
        return __('Unable to resolve certificate for :domain', [
            'domain' => $this->monitor->domain,
        ]);
    }

    public function description(): string
    {
        return __('Error: :error', [
            'error' => $this->error,
        ]);
    }

    public static function info(): ?string
    {
        return __('Triggered when an SSL certificate cannot be retrieved or validated.');
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

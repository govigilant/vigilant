<?php

namespace Vigilant\Dns\Notifications;

use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Models\DnsMonitorHistory;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class RecordNotResolvedNotification extends Notification implements HasSite
{
    public static string $name = 'Unable to resolve DNS record';

    public Level $level = Level::Critical;

    public function __construct(public DnsMonitor $monitor, public ?DnsMonitorHistory $previous) {}

    public function title(): string
    {
        return __('Unable to resolve DNS record :record', ['record' => $this->monitor->record]);
    }

    public function description(): string
    {
        return __('From: :old, To: :new', [
            'old' => $this->previous?->value ?? 'None',
            'new' => $this->monitor->value ?? 'None',
        ]);
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }

    public function uniqueId(): string|int
    {
        return $this->monitor->id;
    }
}

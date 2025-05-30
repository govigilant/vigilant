<?php

namespace Vigilant\Dns\Notifications;

use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Models\DnsMonitorHistory;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class RecordChangedNotification extends Notification implements HasSite
{
    public static string $name = 'DNS Record Changed';

    public Level $level = Level::Warning;

    public function __construct(public DnsMonitor $monitor, public DnsMonitorHistory $previous) {}

    public function title(): string
    {
        return __('DNS Record :type :record has been changed', ['type' => $this->monitor->type->name, 'record' => $this->monitor->record]);
    }

    public function description(): string
    {
        return __('The :type record for :record has been changed from :old to :new at :changedate', [
            'type' => $this->monitor->type->name,
            'record' => $this->monitor->record,
            'old' => $this->previous->value ?? '?',
            'new' => $this->monitor->value ?? '?',
            'changedate' => $this->previous->created_at?->toDateString() ?? '?',
        ]);
    }

    public function level(): Level
    {
        $critical = [
            Type::A,
            Type::AAAA,
            Type::NS,
            Type::MX,
        ];

        return in_array($this->monitor->type, $critical)
            ? Level::Critical
            : Level::Warning;
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

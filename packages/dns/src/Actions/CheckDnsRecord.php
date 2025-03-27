<?php

namespace Vigilant\Dns\Actions;

use Vigilant\Core\Services\TeamService;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Notifications\RecordChangedNotification;
use Vigilant\Dns\Notifications\RecordNotResolvedNotification;

class CheckDnsRecord
{
    public function __construct(
        protected ResolveRecord $record,
        protected TeamService $teamService
    ) {}

    public function check(DnsMonitor $monitor): void
    {
        $resolved = $this->record->resolve($monitor->type, $monitor->record);

        if ($resolved === $monitor->value) {
            return;
        }

        $this->teamService->setTeamById($monitor->team_id);

        if ($resolved === null) {
            $monitor->update([
                'value' => null,
            ]);

            RecordNotResolvedNotification::notify($monitor, $monitor->history()->latest()->first());

            return;
        }

        $previous = $monitor->history()->create([
            'type' => $monitor->type,
            'value' => $monitor->value ?? '',
            'geoip' => $monitor->geoip,
        ]);

        $monitor->update([
            'value' => $resolved,
        ]);

        RecordChangedNotification::notify($monitor, $previous);
    }
}

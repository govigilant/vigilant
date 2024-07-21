<?php

namespace Vigilant\Dns\Actions;

use Vigilant\Core\Services\TeamService;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Notifications\RecordChangedNotification;

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

        $previous = $monitor->history()->create([
            'type' => $monitor->type,
            'value' => $monitor->value,
            'geoip' => $monitor->geoip,
        ]);

        $monitor->update([
            'value' => $resolved,
        ]);

        RecordChangedNotification::notify($monitor, $previous);
    }
}

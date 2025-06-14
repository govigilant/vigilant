<?php

namespace Vigilant\Cve\Actions;

use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Notifications\CveMatchedNotification;

class MatchCve
{
    public function match(CveMonitor $monitor, Cve $cve): void
    {
        $matches = str($cve->description)->lower()->contains(strtolower($monitor->keyword));

        if (! $matches) {
            return;
        }

        $monitor->matches()->firstOrCreate([
            'cve_id' => $cve->id,
        ]);

        CveMatchedNotification::notify($monitor, $cve);
    }
}

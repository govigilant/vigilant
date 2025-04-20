<?php

namespace Vigilant\Cve\Actions;

use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

class MatchExistingCves
{
    public function match(CveMonitor $monitor): void
    {
        Cve::query()
            ->whereRaw('LOWER(description) LIKE ?', ['%'.strtolower($monitor->keyword).'%'])
            ->select('id')
            ->get()
            ->each(function ($cve) use ($monitor): void {
                $monitor->matches()->firstOrCreate([
                    'cve_id' => $cve->id,
                ], [
                    'cve_monitor_id' => $monitor->id,
                ]);
            });
    }
}

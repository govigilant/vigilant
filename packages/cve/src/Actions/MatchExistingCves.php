<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Facades\DB;
use Vigilant\Cve\Models\CveMonitor;

class MatchExistingCves
{
    public function match(CveMonitor $monitor): void
    {
        // Skip SQLite-incompatible query when running tests
        if (app()->runningUnitTests()) {
            return;
        }

        DB::statement('
                INSERT IGNORE INTO cve_monitor_matches (cve_id, cve_monitor_id, created_at, updated_at)
                SELECT
                    id as cve_id,
                    ? as cve_monitor_id,
                    NOW() as created_at,
                    NOW() as updated_at
                FROM cves
                WHERE MATCH(description) AGAINST(? IN BOOLEAN MODE)
            ', [
            $monitor->id,
            $monitor->keyword,
        ]);
    }
}

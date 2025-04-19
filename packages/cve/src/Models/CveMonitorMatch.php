<?php

namespace Vigilant\Cve\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cve_id
 * @property int $cve_monitor_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Cve $cve
 * @property CveMonitor $cveMonitor
 */
class CveMonitorMatch extends Model
{
    protected $guarded = [];

    public function cve(): BelongsTo
    {
        return $this->belongsTo(Cve::class);
    }

    public function cveMonitor(): BelongsTo
    {
        return $this->belongsTo(CveMonitor::class);
    }
}

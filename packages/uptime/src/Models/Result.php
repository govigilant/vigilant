<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $checker_id
 * @property int $total_time
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Monitor $monitor
 */
class Result extends Model
{
    protected $table = 'uptime_results';

    protected $guarded = [];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}

<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $checker_id
 * @property Carbon $start
 * @property ?Carbon $end
 * @property ?array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Monitor $monitor
 */
class Downtime extends Model
{
    protected $table = 'uptime_downtimes';

    protected $guarded = [];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'data' => 'array',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}

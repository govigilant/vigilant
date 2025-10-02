<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Concerns\HasDataRetention;

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
    use HasDataRetention;
    use Prunable;

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

    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->where('created_at', '<=', $this->retentionPeriod());
    }
}

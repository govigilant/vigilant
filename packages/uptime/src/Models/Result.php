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
 * @property int $total_time
 * @property ?string $country
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Monitor $monitor
 */
class Result extends Model
{
    use HasDataRetention;
    use Prunable;

    protected $table = 'uptime_results';

    protected $guarded = [];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->where('created_at', '<=', $this->retentionPeriod());
    }
}

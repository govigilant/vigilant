<?php

namespace Vigilant\Healthchecks\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Concerns\HasDataRetention;

/**
 * @property int $id
 * @property int $healthcheck_id
 * @property ?int $run_id
 * @property string $key
 * @property float $value
 * @property ?string $unit
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Healthcheck $healthcheck
 */
class Metric extends Model
{
    use HasDataRetention;
    use Prunable;

    protected $table = 'healthcheck_metrics';

    protected $guarded = [];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function healthcheck(): BelongsTo
    {
        return $this->belongsTo(Healthcheck::class);
    }

    public function prunable(): Builder
    {
        return static::withoutGlobalScopes()->where('created_at', '<=', $this->retentionPeriod());
    }
}

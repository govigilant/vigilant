<?php

namespace Vigilant\Healthchecks\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Concerns\HasDataRetention;
use Vigilant\Healthchecks\Enums\Status;

/**
 * @property int $id
 * @property int $healthcheck_id
 * @property ?int $run_id
 * @property string $key
 * @property Status $status
 * @property ?string $message
 * @property ?array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Healthcheck $healthcheck
 */
class Result extends Model
{
    use HasDataRetention;
    use Prunable;

    protected $table = 'healthcheck_results';

    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'data' => 'array',
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

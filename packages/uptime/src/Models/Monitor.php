<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Enums\Type;

/**
 * @property int $id
 * @property int $site_id
 * @property string $name
 * @property Type $type
 * @property array $settings
 * @property string $interval
 * @property int $retries
 * @property int $timeout
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property Collection<int, Result> $results
 * @property Collection<int, Downtime> $downtimes
 */
class Monitor extends Model
{
    protected $table = 'uptime_monitors';

    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
        'settings' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function downtimes(): HasMany
    {
        return $this->hasMany(Downtime::class);
    }

}

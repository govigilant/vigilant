<?php

namespace Vigilant\Healthchecks\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Observers\HealthcheckObserver;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property bool $enabled
 * @property string $domain
 * @property Type $type
 * @property ?string $endpoint
 * @property string $token
 * @property ?Carbon $next_check_at
 * @property ?Carbon $last_check_at
 * @property int $interval
 * @property ?Status $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property ?Team $team
 * @property Collection<int, Result> $results
 * @property Collection<int, Metric> $metrics
 */
#[ScopedBy([TeamScope::class])]
#[ObservedBy([TeamObserver::class, HealthcheckObserver::class])]
class Healthcheck extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'type' => Type::class,
        'status' => Status::class,
        'next_check_at' => 'datetime',
        'last_check_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class);
    }
}

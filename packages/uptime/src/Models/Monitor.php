<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Database\Factories\MonitorFactory;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Observers\MonitorObserver;
use Vigilant\Users\Models\Team;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property string $name
 * @property Type $type
 * @property array $settings
 * @property string $interval
 * @property int $retries
 * @property int $timeout
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property ?Team $team
 * @property Collection<int, Result> $results
 * @property Collection<int, Result> $aggregatedResults
 * @property Collection<int, Downtime> $downtimes
 */
#[ObservedBy([MonitorObserver::class])]
#[ScopedBy([TeamScope::class])]
class Monitor extends Model
{
    use HasFactory;

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

    public function aggregatedResults(): HasMany
    {
        return $this->hasMany(ResultAggregate::class);
    }

    public function downtimes(): HasMany
    {
        return $this->hasMany(Downtime::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function currentDowntime(): ?Downtime
    {
        /** @var ?Downtime $downtime */
        $downtime = $this->downtimes()
            ->whereNull('end')
            ->orderByDesc('start')
            ->first();

        return $downtime;
    }

    protected static function newFactory(): MonitorFactory
    {
        return new MonitorFactory;
    }
}

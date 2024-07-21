<?php

namespace Vigilant\Dns\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Observers\GeoipObserver;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property Type $type
 * @property string $record
 * @property ?string $value
 * @property ?array $geoip
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property Collection<int, DnsMonitorHistory> $history
 */
#[ObservedBy([TeamObserver::class, GeoipObserver::class])]
#[ScopedBy(TeamScope::class)]
class DnsMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
        'geoip' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(DnsMonitorHistory::class);
    }
}

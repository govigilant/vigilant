<?php

namespace Vigilant\Lighthouse\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property bool $enabled
 * @property ?int $site_id
 * @property int $team_id
 * @property string $url
 * @property array $settings
 * @property int $interval
 * @property ?Carbon $next_run
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property Collection<int, LighthouseResult> $lighthouseResults
 */
#[ObservedBy([TeamObserver::class])]
#[ScopedBy([TeamScope::class])]
class LighthouseMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'bool',
        'settings' => 'array',
        'next_run' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function lighthouseResults(): HasMany
    {
        return $this->hasMany(LighthouseResult::class);
    }
}

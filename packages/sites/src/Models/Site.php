<?php

namespace Vigilant\Sites\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Lighthouse\Models\LighthouseSite;
use Vigilant\Sites\Observers\SiteObserver;
use Vigilant\Uptime\Models\Monitor as UptimeMonitor;

/**
 * @property int $id
 * @property int $team_id
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, UptimeMonitor> $uptimeMonitors
 * @property Collection<int, LighthouseSite> $lighthouseMonitors
 */
#[ObservedBy([SiteObserver::class])]
#[ScopedBy([TeamScope::class])]
class Site extends Model
{
    protected $guarded = [];

    public function uptimeMonitors(): HasMany
    {
        return $this->hasMany(UptimeMonitor::class);
    }

    public function lighthouseMonitors(): HasMany
    {
        return $this->hasMany(LighthouseSite::class);
    }
}

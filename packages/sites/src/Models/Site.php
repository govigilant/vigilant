<?php

namespace Vigilant\Sites\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Sites\Observers\SiteObserver;
use Vigilant\Uptime\Models\Monitor;

/**
 * @property int $id
 * @property int $team_id
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, Monitor> $monitors
 */
#[ObservedBy([SiteObserver::class])]
#[ScopedBy([TeamScope::class])]
class Site extends Model
{
    protected $guarded = [];

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }
}

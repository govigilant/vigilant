<?php

namespace Vigilant\Cve\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Cve\Observers\CveMonitorObserver;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property bool $enabled
 * @property string $keyword
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Site $site
 * @property Team $team
 */
#[ScopedBy(TeamScope::class)]
#[ObservedBy([TeamObserver::class, CveMonitorObserver::class])]
class CveMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(CveMonitorMatch::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}

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
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $lighthouse_monitor_id
 * @property int $team_id
 * @property float $performance
 * @property float $accessibility
 * @property float $best_practices
 * @property float $seo
 * @property bool $aggregated
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property LighthouseMonitor $lighthouseSite
 * @property Collection<int, LighthouseResultAudit> $audits
 */
#[ObservedBy([TeamObserver::class])]
#[ScopedBy([TeamScope::class])]
class LighthouseResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aggregated' => 'bool',
    ];

    public function lighthouseSite(): BelongsTo
    {
        return $this->belongsTo(LighthouseMonitor::class, 'lighthouse_monitor_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(LighthouseResultAudit::class);
    }
}

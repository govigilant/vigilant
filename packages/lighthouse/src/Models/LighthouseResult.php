<?php

namespace Vigilant\Lighthouse\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $lighthouse_site_id
 * @property int $team_id
 * @property float $performance
 * @property float $accessibility
 * @property float $best_practices
 * @property float $seo
 * @property bool $aggregated
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?LighthouseSite $lighthouseSite
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
        return $this->belongsTo(LighthouseSite::class);
    }
}

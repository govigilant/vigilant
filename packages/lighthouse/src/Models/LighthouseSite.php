<?php

namespace Vigilant\Lighthouse\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property string $url
 * @property array $settings
 * @property string $interval
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, LighthouseResult> $lighthouseResults
 */
#[ObservedBy([TeamObserver::class])]
#[ScopedBy([TeamScope::class])]
class LighthouseSite extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    public function lighthouseResults(): HasMany
    {
        return $this->hasMany(LighthouseResult::class);
    }
}

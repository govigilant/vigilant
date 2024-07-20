<?php

namespace Vigilant\Dns\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Dns\Enums\Type;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $dns_monitor_id
 * @property int $team_id
 * @property Type $type
 * @property string $value
 * @property ?array $geoip
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?DnsMonitor $monitor
 */
#[ObservedBy(TeamObserver::class)]
#[ScopedBy(TeamScope::class)]
class DnsMonitorHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
        'geoip' => 'array',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(DnsMonitor::class);
    }
}

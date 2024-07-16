<?php

namespace Vigilant\Dns\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Dns\Enums\Type;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $site_id
 * @property int $team_id
 * @property Type $type
 * @property string $record
 * @property string $value
 * @property ?array $geoip
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[ObservedBy(TeamObserver::class)]
#[ScopedBy(TeamScope::class)]
class DnsMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
        'geoip' => 'array',
    ];
}

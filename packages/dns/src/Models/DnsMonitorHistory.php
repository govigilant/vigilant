<?php

namespace Vigilant\Dns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Dns\Enums\Type;

/**
 * @property int $id
 * @property int $site_id
 * @property int $team_id
 * @property Type $type
 * @property string $value
 * @property ?array $geoip
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class DnsMonitorHistory extends Model
{
    protected $casts = [
        'type' => Type::class,
        'geoip' => 'array',
    ];
}

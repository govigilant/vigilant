<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Uptime\Enums\OutpostStatus;

/**
 * @property int $id
 * @property string $ip
 * @property int $port
 * @property string $external_ip
 * @property OutpostStatus $status
 * @property ?string $country
 * @property ?float $latitude
 * @property ?float $longitude
 * @property Carbon $last_available_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Outpost extends Model
{
    protected $table = 'uptime_outposts';

    protected $guarded = [];

    protected $casts = [
        'status' => OutpostStatus::class,
        'latitude' => 'float',
        'longitude' => 'float',
        'last_available_at' => 'datetime',
    ];
}

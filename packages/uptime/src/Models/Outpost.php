<?php

namespace Vigilant\Uptime\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Observers\OutpostObserver;

/**
 * @property int $id
 * @property string $ip
 * @property int $port
 * @property string $external_ip
 * @property OutpostStatus $status
 * @property ?string $country
 * @property ?float $latitude
 * @property ?float $longitude
 * @property bool $geoip_automatic
 * @property Carbon $last_available_at
 * @property ?Carbon $unavailable_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[ObservedBy([OutpostObserver::class])]
class Outpost extends Model
{
    protected $table = 'uptime_outposts';

    protected $guarded = [];

    protected $casts = [
        'status' => OutpostStatus::class,
        'latitude' => 'float',
        'longitude' => 'float',
        'geoip_automatic' => 'boolean',
        'last_available_at' => 'datetime',
        'unavailable_at' => 'datetime',
    ];

    public function url(): string
    {
        return "https://{$this->ip}:{$this->port}";
    }
}

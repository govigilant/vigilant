<?php

namespace Vigilant\Certificates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;

/**
 * @property int $id
 * @property int $site_id
 * @property int $team_id
 * @property ?Carbon $next_check
 * @property string $domain
 * @property int $port
 * @property ?string $serial_number
 * @property ?Carbon $valid_from
 * @property ?Carbon $valid_to
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Site $site
 * @property Team $team
 */
class CertificateMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'next_check' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

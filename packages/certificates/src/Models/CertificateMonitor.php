<?php

namespace Vigilant\Certificates\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $site_id
 * @property int $team_id
 * @property bool $enabled
 * @property ?Carbon $next_check
 * @property string $domain
 * @property int $port
 * @property ?string $serial_number
 * @property ?string $fingerprint
 * @property ?string $protocol
 * @property ?Carbon $valid_from
 * @property ?Carbon $valid_to
 * @property ?array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Site $site
 * @property Team $team
 * @property Collection<int, CertificateMonitorHistory> $history
 */
#[ObservedBy(TeamObserver::class)]
#[ScopedBy(TeamScope::class)]
class CertificateMonitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'next_check' => 'datetime',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'data' => 'array',
    ];

    public function history(): HasMany
    {
        return $this->hasMany(CertificateMonitorHistory::class);
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

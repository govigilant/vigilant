<?php

namespace Vigilant\Certificates\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $certificate_monitor_id
 * @property int $team_id
 * @property ?string $serial_number
 * @property ?string $protocol
 * @property ?Carbon $valid_from
 * @property ?Carbon $valid_to
 * @property ?array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property CertificateMonitor $certificateMonitor
 * @property Team $team
 */
#[ObservedBy(TeamObserver::class)]
#[ScopedBy(TeamScope::class)]
class CertificateMonitorHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'data' => 'array',
    ];

    public function certificateMonitor(): BelongsTo
    {
        return $this->belongsTo(CertificateMonitor::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}

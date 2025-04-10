<?php

namespace Vigilant\Notifications\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Notifications\Scopes\HistoryTeamScope;

/**
 * @property int $id
 * @property int $trigger_id
 * @property int $channel_id
 * @property string $notification
 * @property string $uniqueId
 * @property array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Trigger $trigger
 * @property ?Channel $channel
 */
#[ScopedBy([HistoryTeamScope::class])]
class History extends Model
{
    protected $table = 'notification_history';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}

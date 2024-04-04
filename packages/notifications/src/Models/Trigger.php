<?php

namespace Vigilant\Notifications\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Notifications\Observers\TriggerObserver;
use Vigilant\Users\Models\Team;

/**
 * @property int $id
 * @property int $team_id
 * @property string $notification
 * @property array $conditions
 * @property bool $all_channels
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Team $team
 * @property Collection<int, Channel> $channels
 * @property Collection<int, History> $history
 */
#[ObservedBy([TriggerObserver::class])]
class Trigger extends Model
{
    protected $table = 'notification_triggers';

    protected $guarded = [];

    protected $casts = [
        'conditions' => 'array',
        'all_channels' => 'bool',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'notification_channel_notification_trigger');
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }
}

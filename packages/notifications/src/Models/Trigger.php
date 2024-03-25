<?php

namespace Vigilant\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Users\Models\Team;

/**
 * @property int $id
 * @property int $team_id
 * @property string $notification
 * @property array $conditions
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Team $team
 * @property Collection<int, Channel> $channels
 */
class Trigger extends Model
{
    protected $table = 'notification_triggers';

    public function team(): BelongsTo
    {
       return $this->belongsTo(Team::class);
    }

    public function channels(): BelongsToMany
    {
       return $this->belongsToMany(Channel::class);
    }
}

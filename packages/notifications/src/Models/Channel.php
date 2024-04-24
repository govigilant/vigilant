<?php

namespace Vigilant\Notifications\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Notifications\Observers\ChannelObserver;

/**
 * @property int $id
 * @property int $team_id
 * @property string $channel
 * @property array $settings
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, History> $history
 */
#[ObservedBy([ChannelObserver::class])]
#[ScopedBy([TeamScope::class])]
class Channel extends Model
{
    protected $table = 'notification_channels';

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    public function title(): string
    {
        return $this->channel::$name;
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }
}

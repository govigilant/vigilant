<?php

namespace Vigilant\Notifications\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Notifications\Observers\ChannelObserver;

/**
 * @property int $id
 * @property int $team_id
 * @property string $channel
 * @property array $settings
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[ObservedBy([ChannelObserver::class])]
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
}

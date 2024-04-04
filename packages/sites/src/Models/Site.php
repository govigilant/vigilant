<?php

namespace Vigilant\Sites\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Vigilant\Sites\Observers\SiteObserver;

/**
 * @property int $id
 * @property int $team_id
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
#[ObservedBy([SiteObserver::class])]
class Site extends Model
{
    protected $guarded = [];
}

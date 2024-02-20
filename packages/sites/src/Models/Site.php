<?php

namespace Vigilant\Sites\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Site extends Model
{
    protected $guarded = [];
}

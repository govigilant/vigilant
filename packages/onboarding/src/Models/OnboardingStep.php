<?php

namespace Vigilant\OnBoarding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Users\Models\Team;

/**
 * @property int $id
 * @property int $team_id
 * @property ?string $step
 * @property ?Carbon $finished_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Team $team
 */
class OnboardingStep extends Model
{
    protected $table = 'team_onboarding_step';

    protected $guarded = [];

    public function team(): BelongsTo
    {
       return $this->belongsTo(Team::class);
    }
}

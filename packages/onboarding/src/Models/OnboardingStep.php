<?php

namespace Vigilant\OnBoarding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property ?string $step
 * @property ?Carbon $finished_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class OnboardingStep extends Model
{
    protected $table = 'team_onboarding_step';

    protected $guarded = [];
}

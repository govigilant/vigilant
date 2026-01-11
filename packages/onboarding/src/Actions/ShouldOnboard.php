<?php

namespace Vigilant\OnBoarding\Actions;

use Vigilant\Core\Services\TeamService;
use Vigilant\OnBoarding\Models\OnboardingStep;
use Vigilant\Sites\Models\Site;

class ShouldOnboard
{
    public function __construct(protected TeamService $teamService) {}

    public function shouldOnboard(): bool
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        $team = $teamService->team();

        /** @var ?OnboardingStep $onBoard */
        $onBoard = OnboardingStep::query()
            ->where('team_id', '=', $team->id)
            ->where('step', '=', 'complete')
            ->first();

        if ($onBoard !== null && $onBoard->finished_at !== null) {
            return false;
        }

        $siteCount = Site::query()->where('team_id', '=', $team->id)->count();

        if ($siteCount > 0) {
            return true;
        }

        return true;
    }
}

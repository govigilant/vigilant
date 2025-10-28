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
        return true;
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        $team = $teamService->team();

        $siteCount = Site::query()->where('team_id', '=', $team->id)->count();

        if ($siteCount > 0) {
            return false;
        }

        /** @var ?OnboardingStep $onBoard */
        $onBoard = OnboardingStep::query()->firstWhere('team_id', '=', $team->id);

        return $onBoard === null || $onBoard->finished_at === null;
    }
}

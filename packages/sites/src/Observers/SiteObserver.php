<?php

namespace Vigilant\Sites\Observers;

use Vigilant\Core\Services\TeamService;
use Vigilant\Sites\Models\Site;

class SiteObserver
{
    public function creating(Site $site): void
    {
        $teamService = app(TeamService::class);

        $site->team_id = $teamService->team()->id;
    }
}

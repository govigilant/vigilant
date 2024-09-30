<?php

use Illuminate\Support\Carbon;
use Vigilant\Core\Services\TeamService;

if (! function_exists('teamTimezone')) {
    function teamTimezone(Carbon $carbon): Carbon
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        return $carbon->timezone($teamService->team()->timezone ?? 'UTC');
    }
}

if (! function_exists('ce')) {
    function ce(): bool
    {
        return config('core.edition', 'ce') === 'ce';
    }
}

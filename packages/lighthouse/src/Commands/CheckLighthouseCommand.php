<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\CheckLighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResult;

class CheckLighthouseCommand extends Command
{
    protected $signature = 'lighthouse:check {resultId}';

    protected $description = 'Check a lighthouse result';

    public function handle(CheckLighthouseResult $lighthouseResult, TeamService $teamService): int
    {
        /** @var int $resultId */
        $resultId = (int) $this->argument('resultId');

        /** @var LighthouseResult $result */
        $result = LighthouseResult::query()->withoutGlobalScopes()->findOrFail($resultId);

        $teamService->setTeamById($result->team_id);

        $lighthouseResult->check($result);

        return static::SUCCESS;
    }
}

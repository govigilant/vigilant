<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\AggregateLighthouseBatch;
use Vigilant\Lighthouse\Models\LighthouseResult;

class AggregateLighthouseBatchCommand extends Command
{
    protected $signature = 'lighthouse:aggregate-batch {resultId}';

    protected $description = 'Check a lighthouse result';

    public function handle(AggregateLighthouseBatch $aggregator, TeamService $teamService): int
    {
        /** @var int $resultId */
        $resultId = (int) $this->argument('resultId');

        /** @var LighthouseResult $result */
        $result = LighthouseResult::query()
            ->withoutGlobalScopes()
            ->whereNotNull('batch_id')
            ->where('id', '=', $resultId)
            ->firstOrFail();

        $teamService->setTeamById($result->team_id);

        $aggregator->aggregateBatch($result->lighthouseSite, $result->batch_id); // @phpstan-ignore-line

        return static::SUCCESS;
    }
}

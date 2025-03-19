<?php

namespace Vigilant\Lighthouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\RunLighthouse;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class RunLighthouseJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public LighthouseMonitor $site, public ?string $batchId = null)
    {
        $this->onQueue(config('lighthouse.queue'));
    }

    public function handle(RunLighthouse $lighthouse, TeamService $teamService): void
    {
        $teamService->setTeamById($this->site->team_id);
        $lighthouse->run($this->site, $this->batchId);
    }

    public function uniqueId(): int
    {
        return $this->site->id;
    }
}

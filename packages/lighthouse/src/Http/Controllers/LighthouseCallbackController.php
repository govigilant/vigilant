<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\ProcessLighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseCallbackController extends Controller
{
    public function result(
        int $monitorId,
        string $batchId,
        string $worker,
        Request $request,
        ProcessLighthouseResult $processor,
        TeamService $teamService
    ): void {
        cache()->forget('lighthouse:worker:'.$worker);

        $monitor = LighthouseMonitor::query()
            ->withoutGlobalScopes()
            ->findOrFail($monitorId);

        $teamService->setTeamById($monitor->team_id);
        $result = $request->only(['categories', 'audits']);

        $processor->process($monitor, $batchId, $result);
    }
}

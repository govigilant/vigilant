<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class RunLighthouse
{
    public function __construct(protected CheckLighthouseResult $lighthouseResult) {}

    public function run(LighthouseMonitor $monitor, ?string $batchId): void
    {
        if ($batchId === null) {
            $batchId = str()->uuid();

            $monitor->update([
                'next_run' => now()->addMinutes($monitor->interval),
            ]);
        }

        $workers = config()->array('lighthouse.workers');
        $worker = null;

        foreach ($workers as $worker) {
            $lockKey = 'lighthouse:worker:'.$worker;

            if (cache()->has($lockKey)) {
                continue;
            }

            $worker = $worker;

            cache()->put($lockKey, true, now()->addMinutes(5));

            break;
        }

        if ($worker === null) {
            logger()->warning('No available workers to run Lighthouse job');

            return;
        }

        $vigilantUrl = config()->string('lighthouse.lighthouse_app_url');

        Http::baseUrl($worker)
            ->post('lighthouse', [
                'website' => $monitor->url,
                'callback_url' => $vigilantUrl.URL::signedRoute('lighthouse.callback', ['monitorId' => $monitor->id, 'batch' => $batchId, 'worker' => $worker], absolute: false),
            ])
            ->throw();

        $monitor->update([
            'run_started_at' => now(),
        ]);
    }
}

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
        $worker = $this->getAvailableWorker();

        if ($worker === null) {
            logger()->warning('No available workers to run Lighthouse job');

            return;
        }

        if ($batchId === null) {
            $batchId = str()->uuid();

            $monitor->update([
                'next_run' => now()->addMinutes($monitor->interval),
            ]);
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

    public function getAvailableWorker(): ?string
    {
        $lockKey = 'lighthouse:worker:lock';
        $workers = config()->array('lighthouse.workers');

        $lock = cache()->lock($lockKey, 5);

        if (! $lock->get()) {
            return null; // Another process is selecting a worker
        }

        try {
            foreach ($workers as $worker) {
                $workerCacheKey = 'lighthouse:worker:'.$worker;

                if (cache()->has($workerCacheKey)) {
                    continue;
                }

                cache()->put($workerCacheKey, true, now()->addMinutes(5));

                return $worker;
            }
        } finally {
            $lock->release();
        }

        return null;
    }
}

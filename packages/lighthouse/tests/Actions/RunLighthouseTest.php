<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\RunLighthouse;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Tests\TestCase;

class RunLighthouseTest extends TestCase
{
    #[Test]
    public function it_runs_lighthouse(): void
    {
        config()->set('lighthouse.workers', ['worker']);
        config()->set('lighthouse.lighthouse_app_url', 'app');

        Http::fake([
            'worker/lighthouse' => Http::response([], 200),
        ])->preventStrayRequests();

        $monitor = LighthouseMonitor::query()->create([
            'enabled' => true,
            'team_id' => 1,
            'url' => 'vigilant',
            'settings' => [],
            'interval' => 60,
        ]);

        $action = app(RunLighthouse::class);

        $action->run($monitor, null);

        $monitor->refresh();
        $this->assertNotNull($monitor->next_run);
        $this->assertNotNull($monitor->run_started_at);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'worker/lighthouse' &&
                $request['website'] === 'vigilant';
        });
    }

    #[Test]
    public function it_gets_worker(): void
    {
        config()->set('lighthouse.workers', [
            'worker_1',
            'worker_2',
            'worker_3',
        ]);

        $action = app(RunLighthouse::class);

        $this->assertEquals('worker_1', $action->getAvailableWorker());
        $this->assertEquals('worker_2', $action->getAvailableWorker());
        $this->assertEquals('worker_3', $action->getAvailableWorker());
        for ($i = 0; $i < 10; $i++) {
            $this->assertNull($action->getAvailableWorker());
        }

        cache()->forget('lighthouse:worker:worker_1');
        $this->assertEquals('worker_1', $action->getAvailableWorker());

        cache()->flush();
        $this->assertEquals('worker_1', $action->getAvailableWorker());
        $this->assertEquals('worker_2', $action->getAvailableWorker());
        $this->assertEquals('worker_3', $action->getAvailableWorker());
    }
}

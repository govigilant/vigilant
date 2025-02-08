<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\Lighthouse;
use Vigilant\Lighthouse\Jobs\LighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Tests\TestCase;

class LighthouseTest extends TestCase
{
    #[Test]
    public function it_runs_lighthouse(): void
    {
        Bus::fake();

        $output = json_encode([
            'categories' => [
                'performance' => [
                    'score' => 1,
                ],
                'accessibility' => [
                    'score' => 1,
                ],
                'best-practices' => [
                    'score' => 1,
                ],
                'seo' => [
                    'score' => 1,
                ],
            ],
            'audits' => [
                'audit' => [
                    'id' => 'audit',
                    'title' => 'Test Audit',
                    'description' => 'Test Audit',
                    'score' => 1,
                    'scoreDisplayMode' => 'binary',
                ],
            ],
        ]);

        if ($output === false) {
            $this->fail('Invalid JSON');
        }

        Process::fake([
            '*' => Process::result(
                output: $output,
            ),
        ]);

        /** @var Lighthouse $action */
        $action = app(Lighthouse::class);

        /** @var LighthouseMonitor $site */
        $site = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $action->run($site, null);

        /** @var ?LighthouseResult $result */
        $result = $site->lighthouseResults()->first();

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->performance);
        $this->assertEquals(1, $result->accessibility);
        $this->assertEquals(1, $result->best_practices);
        $this->assertEquals(1, $result->seo);

        $this->assertEquals(1, $result->audits()->count());

        Bus::assertDispatched(LighthouseJob::class);
    }
}

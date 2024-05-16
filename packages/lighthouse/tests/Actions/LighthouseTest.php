<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use Illuminate\Support\Facades\Process;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\CheckLighthouseResult;
use Vigilant\Lighthouse\Actions\Lighthouse;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseSite;
use Vigilant\Lighthouse\Tests\TestCase;

class LighthouseTest extends TestCase
{
    #[Test]
    public function it_runs_lighthouse(): void
    {
        $this->mock(CheckLighthouseResult::class, function (MockInterface $mock): void {
            $mock->shouldReceive('check')->once();
        });

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

        /** @var LighthouseSite $site */
        $site = LighthouseSite::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $action->run($site);

        /** @var ?LighthouseResult $result */
        $result = $site->lighthouseResults()->first();

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->performance);
        $this->assertEquals(1, $result->accessibility);
        $this->assertEquals(1, $result->best_practices);
        $this->assertEquals(1, $result->seo);
    }
}

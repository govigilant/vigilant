<?php

namespace Vigilant\Lighthouse\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Jobs\RunLighthouseJob;
use Vigilant\Lighthouse\ServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            \Vigilant\Core\ServiceProvider::class,
            \Vigilant\Users\ServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        TeamService::fake();
        Bus::fake([
            RunLighthouseJob::class,
        ]);
    }
}

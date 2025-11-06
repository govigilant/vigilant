<?php

namespace Vigilant\Healthchecks\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Vigilant\Healthchecks\ServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Configure environment
    }
}

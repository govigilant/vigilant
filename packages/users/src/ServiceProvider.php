<?php

namespace Vigilant\Users;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Vigilant\Core\Services\TeamService;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerPolicies();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/users.php', 'users');

        return $this;
    }

    protected function registerPolicies(): static
    {

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootServices()
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands();
    }

    protected function bootServices(): static
    {
        app()->singleton(TeamService::class);

        return $this;
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/users.php' => config_path('users.php'),
        ], 'config');

        return $this;
    }

    protected function bootMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    protected function bootCommands(): static
    {
        if ($this->app->runningInConsole()) {
            $this->commands([

            ]);
        }

        return $this;
    }
}

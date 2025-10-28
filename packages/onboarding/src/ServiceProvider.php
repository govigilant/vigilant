<?php

namespace Vigilant\OnBoarding;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\OnBoarding\Livewire\Complete;
use Vigilant\OnBoarding\Livewire\ImportDomains;
use Vigilant\OnBoarding\Livewire\NotificationChannel;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/onboarding.php', 'onboarding');

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands()
            ->bootViews()
            ->bootLivewire()
            ->bootRoutes();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/onboarding.php' => config_path('onboarding.php'),
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

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'onboarding');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('onboarding-import-domains', ImportDomains::class);
        Livewire::component('onboarding-monitoring-channel', NotificationChannel::class);
        Livewire::component('onboarding-complete', Complete::class);

        return $this;
    }

    protected function bootRoutes(): static
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware(['web', 'auth'])
                ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));
        }

        return $this;
    }
}

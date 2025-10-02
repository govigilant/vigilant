<?php

namespace Vigilant\Core;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Vigilant\Core\Actions\ResolveDataRetention;
use Vigilant\Core\Contracts\ResolvesDataRetention;
use Vigilant\Core\Facades\Navigation as NavigationFacade;
use Vigilant\Core\Navigation\Navigation;
use Vigilant\Core\Services\TeamService;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerSingletons();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'core');

        return $this;
    }

    protected function registerSingletons(): static
    {
        $this->app->scoped(TeamService::class);
        $this->app->singleton(Navigation::class);

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootActions()
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands()
            ->bootViews()
            ->bootRoutes()
            ->bootNavigation();
    }

    protected function bootActions(): static
    {
        if (ce()) {
            $this->app->singleton(ResolvesDataRetention::class, ResolveDataRetention::class);
        }

        return $this;
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/core.php' => config_path('core.php'),
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'core');

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

    protected function bootNavigation(): static
    {
        NavigationFacade::path(__DIR__.'/../resources/navigation.php');

        return $this;
    }
}

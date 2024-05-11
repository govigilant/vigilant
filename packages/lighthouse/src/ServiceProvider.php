<?php

namespace Vigilant\Lighthouse;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Lighthouse\Livewire\LighthouseSiteForm;
use Vigilant\Lighthouse\Livewire\LighthouseSites;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseSitesTable;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Lighthouse\Commands\LighthouseCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/lighthouse.php', 'lighthouse');

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
            ->bootRoutes()
            ->bootNavigation()
            ->bootNotifications();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/lighthouse.php' => config_path('lighthouse.php'),
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
                LighthouseCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lighthouse');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('lighthouse', LighthouseSites::class);
        Livewire::component('lighthouse-sites-table', LighthouseSitesTable::class);
        Livewire::component('lighthouse-site-form', LighthouseSiteForm::class);


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
        Navigation::path(__DIR__.'/../resources/navigation.php');

        return $this;
    }

    protected function bootNotifications(): static
    {
        NotificationRegistry::registerNotification([

        ]);

        return $this;
    }
}

<?php

namespace Vigilant\Uptime;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Http\Livewire\Charts\LatencyChart;
use Vigilant\Uptime\Http\Livewire\Tables\MonitorTable;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;
use Vigilant\Uptime\Notifications\DowntimeNotification;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerActions();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uptime.php', 'uptime');

        return $this;
    }

    protected function registerActions(): static
    {

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
            __DIR__.'/../config/uptime.php' => config_path('uptime.php'),
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
                CheckUptimeCommand::class,
                AggregateResultsCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'uptime');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('uptime', UptimeMonitors::class);
        Livewire::component('uptime-monitor-form', UptimeMonitorForm::class);
        Livewire::component('uptime-monitor-table', MonitorTable::class);

        Livewire::component('monitor-latency-chart', LatencyChart::class);

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
            DowntimeNotification::class,
        ]);

        return $this;
    }
}

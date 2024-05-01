<?php

namespace Vigilant\Uptime;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Http\Livewire\Charts\LatencyChart;
use Vigilant\Uptime\Http\Livewire\Tables\MonitorTable;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;
use Vigilant\Uptime\Listeners\DowntimeEndNotificationListener;
use Vigilant\Uptime\Listeners\DowntimeStartNotificationListener;
use Vigilant\Uptime\Notifications\DowntimeStartNotification;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerEvents();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uptime.php', 'uptime');

        return $this;
    }

    protected function registerEvents(): static
    {
        Event::listen(DowntimeStartEvent::class, [
            DowntimeStartNotificationListener::class,
        ]);

        Event::listen(DowntimeEndEvent::class, [
            DowntimeEndNotificationListener::class,
        ]);

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
            DowntimeStartNotification::class,
        ]);

        return $this;
    }
}

<?php

namespace Vigilant\Uptime;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Commands\CheckLatencyCommand;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Commands\GenerateRootCaCommand;
use Vigilant\Uptime\Commands\ScheduleUptimeChecksCommand;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Events\UptimeCheckedEvent;
use Vigilant\Uptime\Http\Livewire\Charts\ColumnLatencyChart;
use Vigilant\Uptime\Http\Livewire\Charts\LatencyChart;
use Vigilant\Uptime\Http\Livewire\Monitor\Dashboard;
use Vigilant\Uptime\Http\Livewire\Tables\DowntimeTable;
use Vigilant\Uptime\Http\Livewire\Tables\MonitorTable;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;
use Vigilant\Uptime\Listeners\CheckLatencyListener;
use Vigilant\Uptime\Listeners\DowntimeEndNotificationListener;
use Vigilant\Uptime\Listeners\DowntimeStartNotificationListener;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\Conditions\ClosestCountryCondition;
use Vigilant\Uptime\Notifications\Conditions\CountryCondition;
use Vigilant\Uptime\Notifications\Conditions\LatencyMsCondition;
use Vigilant\Uptime\Notifications\Conditions\LatencyPercentCondition;
use Vigilant\Uptime\Notifications\DowntimeEndNotification;
use Vigilant\Uptime\Notifications\DowntimeStartNotification;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;
use Vigilant\Uptime\Notifications\LatencyPeakNotification;
use Vigilant\Users\Models\User;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uptime.php', 'uptime');

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
            ->bootEvents()
            ->bootRatelimiting()
            ->bootNavigation()
            ->bootNotifications()
            ->bootGates()
            ->bootPolicies();
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
                ScheduleUptimeChecksCommand::class,
                CheckLatencyCommand::class,
                GenerateRootCaCommand::class,
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
        Livewire::component('uptime-downtime-table', DowntimeTable::class);

        Livewire::component('monitor-column-latency-chart', ColumnLatencyChart::class);
        Livewire::component('monitor-latency-chart', LatencyChart::class);

        Livewire::component('monitor-dashboard', Dashboard::class);

        return $this;
    }

    protected function bootRoutes(): static
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware(['web', 'auth'])
                ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));

            Route::prefix('api')
                ->middleware(['api'])
                ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/api.php'));
        }

        return $this;
    }

    protected function bootEvents(): static
    {
        Event::listen(DowntimeStartEvent::class, DowntimeStartNotificationListener::class);

        Event::listen(DowntimeEndEvent::class, DowntimeEndNotificationListener::class);

        Event::listen(UptimeCheckedEvent::class, CheckLatencyListener::class);

        return $this;
    }

    protected function bootRatelimiting(): static
    {
        RateLimiter::for('uptime-ips', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip);
        });

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
            DowntimeEndNotification::class,
            LatencyChangedNotification::class,
            LatencyPeakNotification::class,
        ]);

        NotificationRegistry::registerCondition(LatencyChangedNotification::class, [
            LatencyPercentCondition::class,
            LatencyMsCondition::class,
            CountryCondition::class,
            ClosestCountryCondition::class,
        ]);

        NotificationRegistry::registerCondition(LatencyPeakNotification::class, [
            LatencyPercentCondition::class,
            LatencyMsCondition::class,
            CountryCondition::class,
            ClosestCountryCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        Gate::define('use-uptime', function (User $user) {
            return ce();
        });

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(Monitor::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

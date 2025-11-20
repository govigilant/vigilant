<?php

namespace Vigilant\Healthchecks;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Healthchecks\Commands\CheckHealthcheckCommand;
use Vigilant\Healthchecks\Commands\ScheduleHealthchecksCommand;
use Vigilant\Healthchecks\Http\Livewire\Charts\MetricChart;
use Vigilant\Healthchecks\Livewire\HealthcheckDashboard;
use Vigilant\Healthchecks\Livewire\HealthcheckForm;
use Vigilant\Healthchecks\Livewire\Healthchecks;
use Vigilant\Healthchecks\Livewire\Tables\HealthcheckTable;
use Vigilant\Healthchecks\Livewire\Tables\ResultTable;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Notifications\Conditions\CheckKeyCondition;
use Vigilant\Healthchecks\Notifications\Conditions\DiskFullInCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreasePercentCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreaseTimeframeCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricKeyCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricUnitCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricValueCondition;
use Vigilant\Healthchecks\Notifications\Conditions\StatusCondition;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Sites\Conditions\SiteCondition;
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
        $this->mergeConfigFrom(__DIR__.'/../config/healthchecks.php', 'healthchecks');

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
            ->bootNotifications()
            ->bootGates()
            ->bootPolicies();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/healthchecks.php' => config_path('healthchecks.php'),
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
                CheckHealthcheckCommand::class,
                ScheduleHealthchecksCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'healthchecks');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('healthchecks', Healthchecks::class);
        Livewire::component('healthcheck-form', HealthcheckForm::class);
        Livewire::component('healthcheck-table', HealthcheckTable::class);
        Livewire::component('healthcheck-result-table', ResultTable::class);
        Livewire::component('healthcheck-metric-chart', MetricChart::class);
        Livewire::component('healthcheck-dashboard', HealthcheckDashboard::class);

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

    protected function bootNavigation(): static
    {
        Navigation::path(__DIR__.'/../resources/navigation.php');

        return $this;
    }

    protected function bootNotifications(): static
    {
        NotificationRegistry::registerNotification([
            HealthCheckFailedNotification::class,
            MetricNotification::class,
            MetricIncreasingNotification::class,
            DiskUsageNotification::class,
        ]);

        NotificationRegistry::registerCondition(HealthCheckFailedNotification::class, [
            StatusCondition::class,
            CheckKeyCondition::class,
            SiteCondition::class,
        ]);

        NotificationRegistry::registerCondition(MetricNotification::class, [
            MetricKeyCondition::class,
            MetricValueCondition::class,
            MetricUnitCondition::class,
            SiteCondition::class,
        ]);

        NotificationRegistry::registerCondition(MetricIncreasingNotification::class, [
            MetricKeyCondition::class,
            MetricIncreasePercentCondition::class,
            MetricIncreaseTimeframeCondition::class,
            SiteCondition::class,
        ]);

        NotificationRegistry::registerCondition(DiskUsageNotification::class, [
            DiskFullInCondition::class,
            SiteCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        Gate::define('use-healthchecks', function (User $user) {
            return ce();
        });

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(Healthcheck::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

<?php

namespace Vigilant\Cve;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Cve\Commands\ImportAllCvesCommand;
use Vigilant\Cve\Commands\ImportCvesCommand;
use Vigilant\Cve\Commands\MatchCveCommand;
use Vigilant\Cve\Commands\MatchExistingCvesCommand;
use Vigilant\Cve\Livewire\CveMonitorForm;
use Vigilant\Cve\Livewire\Tables\CveMonitorTable;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Notifications\Conditions\KeywordCondition;
use Vigilant\Cve\Notifications\Conditions\ScoreCondition;
use Vigilant\Cve\Notifications\CveMatchedNotification;
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
        $this->mergeConfigFrom(__DIR__.'/../config/cve.php', 'cve');

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootRoutes()
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands()
            ->bootViews()
            ->bootLivewire()
            ->bootNavigation()
            ->bootNotifications()
            ->bootGates()
            ->bootPolicies();
    }

    protected function bootRoutes(): static
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware(['web', 'auth'])
                ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));
        }

        return $this;
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/cve.php' => config_path('cve.php'),
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
                ImportAllCvesCommand::class,
                ImportCvesCommand::class,
                MatchExistingCvesCommand::class,
                MatchCveCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cve');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('cve-monitor-table', CveMonitorTable::class);
        Livewire::component('cve-monitor-form', CveMonitorForm::class);

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
            CveMatchedNotification::class,
        ]);

        NotificationRegistry::registerCondition(CveMatchedNotification::class, [
            SiteCondition::class,
            ScoreCondition::class,
            KeywordCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        if (ce()) {
            Gate::define('use-cve', function (User $user): bool {
                return ce();
            });
        }

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(CveMonitor::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

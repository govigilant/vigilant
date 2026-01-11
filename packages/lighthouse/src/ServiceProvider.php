<?php

namespace Vigilant\Lighthouse;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Lighthouse\Commands\AggregateLighthouseBatchCommand;
use Vigilant\Lighthouse\Commands\AggregateLighthouseResultsCommand;
use Vigilant\Lighthouse\Commands\CheckLighthouseCommand;
use Vigilant\Lighthouse\Commands\LighthouseCommand;
use Vigilant\Lighthouse\Commands\ScheduleLighthouseCommand;
use Vigilant\Lighthouse\Livewire\Charts\LighthouseCategoriesChart;
use Vigilant\Lighthouse\Livewire\Charts\NumericLighthouseChart;
use Vigilant\Lighthouse\Livewire\LighthouseSiteForm;
use Vigilant\Lighthouse\Livewire\LighthouseSites;
use Vigilant\Lighthouse\Livewire\Monitor\Dashboard;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseMonitorsTable;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseResultAuditsTable;
use Vigilant\Lighthouse\Livewire\Tables\LighthouseResultsTable;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditChangesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditPercentCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditTypeCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditValueCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AccessibilityPercentScoreCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AccessibilityScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AccessibilityScoreIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AccessibilityScoreValueCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreChangesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreValueCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\BestPracticesPercentScoreCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\BestPracticesScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\BestPracticesScoreIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\BestPracticesScoreValueCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\PerformancePercentScoreCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\PerformanceScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\PerformanceScoreIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\PerformanceScoreValueCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\SeoPercentPercentScoreCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\SeoScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\SeoScoreIncreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\SeoScoreValueCondition;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
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
            ->bootNotifications()
            ->bootGates()
            ->bootPolicies();
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
                ScheduleLighthouseCommand::class,
                CheckLighthouseCommand::class,
                AggregateLighthouseResultsCommand::class,
                AggregateLighthouseBatchCommand::class,
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
        Livewire::component('lighthouse-sites-table', LighthouseMonitorsTable::class);
        Livewire::component('lighthouse-results-table', LighthouseResultsTable::class);
        Livewire::component('lighthouse-site-form', LighthouseSiteForm::class);
        Livewire::component('lighthouse-categories-chart', LighthouseCategoriesChart::class);
        Livewire::component('lighthouse-result-audits-table', LighthouseResultAuditsTable::class);

        Livewire::component('lighthouse-numeric-chart', NumericLighthouseChart::class);
        Livewire::component('lighthouse-monitor-dashboard', Dashboard::class);

        return $this;
    }

    protected function bootRoutes(): static
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware(['web', 'auth'])
                ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));

            Route::prefix('api/v1/lighthouse')
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
            CategoryScoreChangedNotification::class,
            NumericAuditChangedNotification::class,
        ]);

        NotificationRegistry::registerCondition(CategoryScoreChangedNotification::class, [
            SiteCondition::class,
            // Change-based conditions (new, clearer)
            AverageScoreIncreasesCondition::class,
            AverageScoreDecreasesCondition::class,
            AverageScoreChangesCondition::class,
            PerformanceScoreIncreasesCondition::class,
            PerformanceScoreDecreasesCondition::class,
            AccessibilityScoreIncreasesCondition::class,
            AccessibilityScoreDecreasesCondition::class,
            BestPracticesScoreIncreasesCondition::class,
            BestPracticesScoreDecreasesCondition::class,
            SeoScoreIncreasesCondition::class,
            SeoScoreDecreasesCondition::class,
            // Absolute value conditions
            AverageScoreValueCondition::class,
            PerformanceScoreValueCondition::class,
            AccessibilityScoreValueCondition::class,
            BestPracticesScoreValueCondition::class,
            SeoScoreValueCondition::class,
            // Legacy conditions (kept for backward compatibility)
            AverageScoreCondition::class,
            AccessibilityPercentScoreCondition::class,
            BestPracticesPercentScoreCondition::class,
            PerformancePercentScoreCondition::class,
            SeoPercentPercentScoreCondition::class,
        ]);

        NotificationRegistry::registerCondition(NumericAuditChangedNotification::class, [
            // Change-based conditions (new, clearer)
            AuditIncreasesCondition::class,
            AuditDecreasesCondition::class,
            AuditChangesCondition::class,
            // Absolute value condition
            AuditValueCondition::class,
            // Legacy condition (kept for backward compatibility)
            AuditPercentCondition::class,
            AuditTypeCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        if (ce()) {
            Gate::define('use-lighthouse', function (User $user): bool {
                return ce();
            });
        }

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(LighthouseMonitor::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

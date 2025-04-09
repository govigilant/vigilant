<?php

namespace Vigilant\Certificates;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Certificates\Commands\CheckCertificateCommand;
use Vigilant\Certificates\Commands\CheckCertificatesCommand;
use Vigilant\Certificates\Livewire\Tables\CertificateMonitorsTable;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Certificates\Notifications\CertificateExpiredNotification;
use Vigilant\Certificates\Notifications\CertificateExpiresInDaysNotification;
use Vigilant\Certificates\Notifications\Conditions\DaysCondition;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
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
        $this->mergeConfigFrom(__DIR__.'/../config/certificates.php', 'certificates');

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
            __DIR__.'/../config/certificates.php' => config_path('certificates.php'),
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
                CheckCertificateCommand::class,
                CheckCertificatesCommand::class,

            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'certificates');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('certificate-monitor-table', CertificateMonitorsTable::class);

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
            CertificateExpiresInDaysNotification::class,
            CertificateExpiredNotification::class,
        ]);

        NotificationRegistry::registerCondition(CertificateExpiresInDaysNotification::class, [
            SiteCondition::class,
            DaysCondition::class,
        ]);

        NotificationRegistry::registerCondition(CertificateExpiredNotification::class, [
            SiteCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        if (ce()) {
            Gate::define('use-certificates', function (User $user): bool {
                return ce();
            });
        }

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(CertificateMonitor::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

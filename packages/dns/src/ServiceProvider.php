<?php

namespace Vigilant\Dns;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Dns\Commands\CheckAllDnsRecordsCommand;
use Vigilant\Dns\Commands\CheckDnsRecordCommand;
use Vigilant\Dns\Commands\ResolveGeoIpCommand;
use Vigilant\Dns\Livewire\DnsImport;
use Vigilant\Dns\Livewire\DnsMonitorForm;
use Vigilant\Dns\Livewire\DnsMonitors;
use Vigilant\Dns\Livewire\Tables\DnsMonitorHistoryTable;
use Vigilant\Dns\Livewire\Tables\DnsMonitorTable;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Notifications\Conditions\RecordTypeCondition;
use Vigilant\Dns\Notifications\RecordChangedNotification;
use Vigilant\Dns\Notifications\RecordNotResolvedNotification;
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
        $this->mergeConfigFrom(__DIR__.'/../config/dns.php', 'dns');

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
            __DIR__.'/../config/dns.php' => config_path('dns.php'),
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
                CheckDnsRecordCommand::class,
                CheckAllDnsRecordsCommand::class,
                ResolveGeoIpCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dns');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('dns-monitors', DnsMonitors::class);
        Livewire::component('dns-monitor-form', DnsMonitorForm::class);
        Livewire::component('dns-monitor-table', DnsMonitorTable::class);
        Livewire::component('dns-monitor-history-table', DnsMonitorHistoryTable::class);
        Livewire::component('dns-monitor-import', DnsImport::class);

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
            RecordChangedNotification::class,
            RecordNotResolvedNotification::class,
        ]);

        NotificationRegistry::registerCondition(RecordChangedNotification::class, [
            SiteCondition::class,
            RecordTypeCondition::class,
        ]);

        NotificationRegistry::registerCondition(RecordNotResolvedNotification::class, [
            SiteCondition::class,
            RecordTypeCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        Gate::define('use-dns', function (User $user): bool {
            return ce();
        });

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(DnsMonitor::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

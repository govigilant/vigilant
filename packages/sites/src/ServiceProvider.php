<?php

namespace Vigilant\Sites;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Sites\Conditions\SiteCondition;
use Vigilant\Sites\Http\Livewire\SiteForm;
use Vigilant\Sites\Http\Livewire\Sites;
use Vigilant\Sites\Http\Livewire\Tables\SiteTable;
use Vigilant\Sites\Http\Livewire\Tabs\CertificateMonitor;
use Vigilant\Sites\Http\Livewire\Tabs\Crawler;
use Vigilant\Sites\Http\Livewire\Tabs\DnsMonitors;
use Vigilant\Sites\Http\Livewire\Tabs\LighthouseMonitors;
use Vigilant\Sites\Http\Livewire\Tabs\UptimeMonitor;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Notifications\DowntimeStartNotification;

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
        $this->mergeConfigFrom(__DIR__.'/../config/sites.php', 'sites');

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
            ->bootNotifications()
            ->bootPolicies();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/sites.php' => config_path('sites.php'),
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sites');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('sites', Sites::class);
        Livewire::component('sites.create', SiteForm::class);
        Livewire::component('sites.table', SiteTable::class);

        Livewire::component('sites.tabs.uptime-monitor', UptimeMonitor::class);
        Livewire::component('sites.tabs.lighthouse-monitor', LighthouseMonitors::class);
        Livewire::component('sites.tabs.dns-monitors', DnsMonitors::class);
        Livewire::component('sites.tabs.crawler', Crawler::class);
        Livewire::component('sites.tabs.certificate-monitor', CertificateMonitor::class);

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
        NotificationRegistry::registerCondition(DowntimeStartNotification::class, [
            SiteCondition::class,
        ]);

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(Site::class, AllowAllPolicy::class);
        }

        return $this;
    }
}

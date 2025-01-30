<?php

namespace Vigilant\Crawler;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Core\Policies\AllowAllPolicy;
use Vigilant\Crawler\Commands\CollectCrawlerStatsCommand;
use Vigilant\Crawler\Commands\CrawlUrlsCommand;
use Vigilant\Crawler\Commands\ProcessCrawlerStatesCommand;
use Vigilant\Crawler\Commands\ScheduleCrawlersCommand;
use Vigilant\Crawler\Commands\StartCrawlerCommand;
use Vigilant\Crawler\Events\CrawlerFinishedEvent;
use Vigilant\Crawler\Listeners\CrawlerFinishedListener;
use Vigilant\Crawler\Livewire\Crawler\Dashboard;
use Vigilant\Crawler\Livewire\CrawlerForm;
use Vigilant\Crawler\Livewire\Crawlers;
use Vigilant\Crawler\Livewire\Tables\CrawlerTable;
use Vigilant\Crawler\Livewire\Tables\IssuesTable;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Notifications\RatelimitedNotification;
use Vigilant\Crawler\Notifications\UrlIssuesNotification;
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
        $this->mergeConfigFrom(__DIR__.'/../config/crawler.php', 'crawler');

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
            ->bootNavigation()
            ->bootNotifications()
            ->bootGates()
            ->bootPolicies();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/crawler.php' => config_path('crawler.php'),
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
                StartCrawlerCommand::class,
                CrawlUrlsCommand::class,
                CollectCrawlerStatsCommand::class,
                ProcessCrawlerStatesCommand::class,
                ScheduleCrawlersCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crawler');

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('crawlers', Crawlers::class);
        Livewire::component('crawler-table', CrawlerTable::class);
        Livewire::component('crawler-form', CrawlerForm::class);

        Livewire::component('crawler-dashboard', Dashboard::class);
        Livewire::component('crawler-issues-table', IssuesTable::class);

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

    protected function bootEvents(): static
    {
        Event::listen(CrawlerFinishedEvent::class, CrawlerFinishedListener::class);

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
            UrlIssuesNotification::class,
            RatelimitedNotification::class,
        ]);

        NotificationRegistry::registerCondition(UrlIssuesNotification::class, [
            SiteCondition::class,
        ]);

        NotificationRegistry::registerCondition(RatelimitedNotification::class, [
            SiteCondition::class,
        ]);

        return $this;
    }

    protected function bootGates(): static
    {
        Gate::define('use-crawler', function (User $user): bool {
            return ce();
        });

        return $this;
    }

    protected function bootPolicies(): static
    {
        if (ce()) {
            Gate::policy(Crawler::class, AllowAllPolicy::class);

            Gate::define('create-crawled-url', function (?User $user, Crawler $crawler): bool {
                return true;
            });
        }

        return $this;
    }
}

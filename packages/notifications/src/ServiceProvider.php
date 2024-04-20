<?php

namespace Vigilant\Notifications;

use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Livewire\Livewire;
use Vigilant\Core\Facades\Navigation;
use Vigilant\Notifications\Channels\NtfyChannel;
use Vigilant\Notifications\Channels\WebhookChannel;
use Vigilant\Notifications\Commands\CreateNotificationsCommand;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Http\Livewire\ChannelForm;
use Vigilant\Notifications\Http\Livewire\Channels;
use Vigilant\Notifications\Http\Livewire\Channels\Configuration\Ntfy;
use Vigilant\Notifications\Http\Livewire\Channels\Configuration\Webhook;
use Vigilant\Notifications\Http\Livewire\NotificationForm;
use Vigilant\Notifications\Http\Livewire\Notifications;
use Vigilant\Notifications\Http\Livewire\Tables\ChannelTable;
use Vigilant\Notifications\Http\Livewire\Tables\NotificationTable;
use Vigilant\Notifications\Jobs\CreateNotificationsJob;
use Vigilant\Users\Models\Team;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notifications.php', 'notifications');

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands()
            ->bootViews()
            ->bootEvents()
            ->bootLivewire()
            ->bootRoutes()
            ->bootNavigation()
            ->bootNotificationChannels();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/notifications.php' => config_path('notifications.php'),
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
                CreateNotificationsCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notifications');

        return $this;
    }

    protected function bootEvents(): static
    {
        Team::created(fn (Team $team): PendingDispatch => CreateNotificationsJob::dispatch($team));

        return $this;
    }

    protected function bootLivewire(): static
    {
        Livewire::component('notifications', Notifications::class);
        Livewire::component('notification-table', NotificationTable::class);
        Livewire::component('notification-form', NotificationForm::class);

        Livewire::component('notification-condition-builder', Notifications\Conditions\ConditionBuilder::class);

        Livewire::component('channels', Channels::class);
        Livewire::component('channel-table', ChannelTable::class);
        Livewire::component('channel-form', ChannelForm::class);

        Livewire::component('channel-configuration-webhook', Webhook::class);
        Livewire::component('channel-configuration-ntfy', Ntfy::class);

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

    protected function bootNotificationChannels(): static
    {
        NotificationRegistry::registerChannel([
            NtfyChannel::class,
            WebhookChannel::class,
        ]);

        return $this;
    }
}

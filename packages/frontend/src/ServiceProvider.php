<?php

namespace Vigilant\Frontend;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this
            ->bootViews()
            ->bootLivewire();
    }

    protected function bootViews(): static
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'frontend');

        return $this;
    }

    protected function bootLivewire(): static
    {
        //Livewire::component('sites', Sites::class);

        return $this;
    }
}

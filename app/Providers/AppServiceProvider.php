<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vigilant\Core\Facades\Navigation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Navigation::path(resource_path('navigation.php'));
    }
}

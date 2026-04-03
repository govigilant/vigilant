<?php

require_once '/app/vendor/autoload.php';

$files = array_merge(
    glob('/app/vendor/laravel/framework/src/Illuminate/Support/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Support/Facades/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Collections/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Http/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Routing/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Database/Eloquent/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Database/Query/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Container/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Pipeline/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/View/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Cache/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Session/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Auth/*.php') ?: [],
    glob('/app/vendor/laravel/framework/src/Illuminate/Validation/*.php') ?: [],

    glob('/app/app/Models/*.php') ?: [],

    glob('/app/packages/*/src/Models/*.php') ?: [],
    glob('/app/packages/*/src/Actions/*.php') ?: [],
    glob('/app/packages/*/src/Enums/*.php') ?: [],
    glob('/app/packages/*/src/Data/*.php') ?: [],
    glob('/app/packages/*/src/Contracts/*.php') ?: [],
    glob('/app/packages/*/src/Concerns/*.php') ?: [],
    glob('/app/packages/*/src/Http/Controllers/*.php') ?: [],
    glob('/app/packages/*/src/Http/Resources/*.php') ?: [],
    glob('/app/packages/*/src/Http/Requests/*.php') ?: [],
    glob('/app/packages/*/src/Livewire/*.php') ?: [],
    glob('/app/packages/*/src/Scopes/*.php') ?: [],
    glob('/app/packages/*/src/Observers/*.php') ?: [],

    glob('/app/packages/saas/packages/*/src/Models/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Actions/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Enums/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Data/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Contracts/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Concerns/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Http/Controllers/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Http/Resources/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Http/Requests/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Livewire/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Scopes/*.php') ?: [],
    glob('/app/packages/saas/packages/*/src/Observers/*.php') ?: [],
);

foreach ($files as $file) {
    try {
        if (is_file($file)) {
            opcache_compile_file($file);
        }
    } catch (Throwable) {
        //
    }
}

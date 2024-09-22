<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Crawler\Http\Controllers\CrawlerController;
use Vigilant\Crawler\Livewire\CrawlerForm;
use Vigilant\Crawler\Livewire\Crawlers;

Route::prefix('crawler')
    ->middleware('can:use-crawler')
    ->group(function (): void {
        Route::get('crawler', Crawlers::class)->name('crawler.index');
        Route::get('crawler/create', CrawlerForm::class)->name('crawler.create');
        Route::get('crawler/{crawler}', [CrawlerController::class, 'index'])->name('crawler.view');
        Route::get('crawler/{crawler}/edit', CrawlerForm::class)->name('crawler.edit');
    });

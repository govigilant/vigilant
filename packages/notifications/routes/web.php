<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Notifications\Http\Livewire\ChannelForm;
use Vigilant\Notifications\Http\Livewire\NotificationForm;

Route::prefix('notifications')->group(function () {
    Route::view('/', 'notifications::notifications')->name('notifications');
    Route::get('create', NotificationForm::class)->name('notifications.trigger.create');
    Route::get('edit/{trigger}', NotificationForm::class)->name('notifications.trigger.edit');

    Route::prefix('channels')->group(function () {
        Route::view('/', 'notifications::channels')->name('notifications.channels');
        Route::get('create', ChannelForm::class)->name('notifications.channel.create');
        Route::get('edit/{channel}', ChannelForm::class)->name('notifications.channel.edit');
    });

    Route::view('history', 'notifications::history')->name('notifications.history');
});

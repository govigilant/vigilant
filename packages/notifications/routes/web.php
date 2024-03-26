<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Notifications\Http\Livewire\Notifications;

Route::get('notifications', Notifications::class)->name('notifications');

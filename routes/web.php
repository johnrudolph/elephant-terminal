<?php

use App\Livewire\GameView;
use App\Livewire\HomePage;
use App\Livewire\FriendsListView;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
    Route::get('/games/{game}', GameView::class)->name('games.show');
    Route::get('/dashboard', HomePage::class)->name('dashboard');
    Route::get('/friends', FriendsListView::class)->name('friends');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

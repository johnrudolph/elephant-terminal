<?php

use App\Livewire\GameView;
use App\Livewire\HomePage;
use App\Livewire\PreGameLobby;
use App\Livewire\FriendsListView;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [HomePage::class, 'edit'])->name('dashboard');
    Route::get('/games/{game}', GameView::class)->name('games.show');
    Route::get('/games/{game}/pre-game-lobby', PreGameLobby::class)->name('games.pre-game-lobby.show');
    Route::get('/dashboard', HomePage::class)->name('dashboard');
Route::get('/friends', FriendsListView::class)->name('friends');
});

require __DIR__.'/auth.php';

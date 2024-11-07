<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Livewire\GameView;
use App\Livewire\HomePage;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Route::get('/dashboard', HomePage::class)->name('home')
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::post('/games/new', [GameController::class, 'create'])->name('games.create');
    // Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    // Route::post('/games/{game}/play_tile', [GameController::class, 'play_tile'])->name('games.play_tile');
    // Route::post('/games/{game}/move_elephant', [GameController::class, 'move_elephant'])->name('games.move_elephant');
    // Route::get('/games/{game}/refresh_game', [GameController::class, 'refresh_game'])->name('games.refresh_game');

    // livewire
    Route::get('/dashboard', HomePage::class)->name('dashboard');
    Route::get('/games/{game}', GameView::class)->name('games.show');
});

require __DIR__.'/auth.php';

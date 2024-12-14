<?php

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('games.{game_id}', function (User $user, int $game_id) {
    Log::info('Checking if user is in game', ['game_id' => $game_id, 'user_id' => $user->id]);
    return Game::find($game_id)->players->pluck('user_id')->contains($user->id);
});

Broadcast::channel('users.{user_id}', function (User $user, int $user_id) {
    return (int) $user->id === (int) $user_id;
});

<?php

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('games.{game_id}', function (User $user, int $game_id) {
    return Game::find($game_id)->players->pluck('user_id')->contains($user->id);
});
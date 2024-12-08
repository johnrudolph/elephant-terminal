<?php

use Thunk\Verbs\Facades\Verbs;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Verbs::commitImmediately();
});

it('cancels stale games', function () {
    $this->bootUnjoinedGame();
    $this->travel(6)->minutes();
    Artisan::call('games:cancel-stale-games');
    $this->game->refresh();
    expect($this->game->status)->toBe('abandoned');
});

it('does not cancel non-stale games', function () {
    $this->bootMultiplayerGame();
    Artisan::call('games:cancel-stale-games');
    $this->game->refresh();
    expect($this->game->status)->toBe('active');
});

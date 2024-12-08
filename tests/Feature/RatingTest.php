<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();
});

it('updates player rating after a game', function () {
    $this->bootMultiPlayerGame();

    expect($this->player_1->user->rating)->toBe(1000);
    expect($this->player_2->user->rating)->toBe(1000);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(5, 'right');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(5, 'right');

    expect($this->game->fresh()->status)->toBe('complete');

    expect($this->player_1->user->fresh()->rating > 1000)->toBeTrue();
    expect($this->player_2->user->fresh()->rating < 1000)->toBeTrue();
});

<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();
});

it('can move a tile and the elephant', function () {
    $this->bootMultiplayerGame();

    expect($this->player_1->state()->victory_shape)->toBe('square');

    $fake_board = [
        1 => (string) $this->player_1->id,
        2 => (string) $this->player_1->id,
        3 => null,
        4 => null,
        5 => (string) $this->player_1->id,
        6 => (string) $this->player_1->id,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => null,
        13 => null,
        14 => null,
        15 => null,
        16 => null,
    ];

    expect(
        collect($this->game->state()->victor($fake_board))
            ->contains((string) $this->player_1->id)
        )->toBeTrue();
});

it('can handle line victories', function() {
    $this->bootSinglePlayerGame('hard', 'line');

    expect($this->player_1->state()->victory_shape)->toBe('line');

    $fake_board = [
        1 => (string) $this->player_1->id,
        2 => (string) $this->player_1->id,
        3 => (string) $this->player_1->id,
        4 => (string) $this->player_1->id,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => null,
        13 => null,
        14 => null,
        15 => null,
        16 => null,
    ];

    expect(
        collect($this->game->state()->victor($fake_board))
            ->contains((string) $this->player_1->id)
        )->toBeTrue();
});

it('can handle pyramid victories', function() {
    $this->bootSinglePlayerGame('hard', 'pyramid');

    expect($this->player_1->state()->victory_shape)->toBe('pyramid');

    $fake_board = [
        1 => (string) $this->player_1->id,
        2 => (string) $this->player_1->id,
        3 => (string) $this->player_1->id,
        4 => null,
        5 => null,
        6 => (string) $this->player_1->id,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => null,
        13 => null,
        14 => null,
        15 => null,
        16 => null,
    ];

    // this works in the abstract:

    expect(
        collect($this->game->state()->victor($fake_board))
            ->contains((string) $this->player_1->id)
        )->toBeTrue();

    // but let's test it for real:
    // @todo this works here, but not IRL.

    $this->player_1->playTile(1, 'down');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(2, 'down');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(2, 'down');
    $this->player_1->moveElephant(7, true);
    $this->player_2->playTile(16, 'up');
    $this->player_2->moveElephant(7, true);
    $this->player_1->playTile(3, 'down');

    dd($this->game->state()->victors);

    expect($this->game->fresh()->status)->toBe('complete');
    
    $fake_board = [
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => (string) $this->player_1->id,
        9 => null,
        10 => null,
        11 => (string) $this->player_1->id,
        12 => (string) $this->player_1->id,
        13 => null,
        14 => null,
        15 => null,
        16 => (string) $this->player_1->id,
    ];

    expect(
        collect($this->game->state()->victor($fake_board))
            ->contains((string) $this->player_1->id)
        )->toBeTrue();
});
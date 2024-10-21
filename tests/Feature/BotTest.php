<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();
});

it('can move a tile and the elephant', function () {
    $this->bootSinglePlayerGame();

    $this->player_1->playTile(1, 'right');
    $this->game->refresh();

    expect($this->game->phase)->toBe('move');
    expect($this->game->current_player_id)->toBe((string) $this->player_1->id);

    $this->player_1->moveElephant(2);
    $this->game->refresh();

    expect($this->game->phase)->toBe('tile');
    expect($this->game->current_player_id)->toBe((string) $this->player_1->id);
});

it('blocks the player from winning', function() {
    $this->bootSinglePlayerGame();

    $fake_board = [
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => $this->player_1->id,
        13 => null,
        14 => null,
        15 => $this->player_1->id,
        16 => $this->player_1->id,
    ];

    $bot_move = $this->game->state()->selectBotTileMove($fake_board);

    expect(collect([12, 15, 16]))->toContain($bot_move['space']);
});

it('wins if it can', function() {
    $this->bootSinglePlayerGame();

    $fake_board = [
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => $this->player_2->id,
        13 => null,
        14 => null,
        15 => $this->player_2->id,
        16 => $this->player_2->id,
    ];

    $bot_move = $this->game->state()->selectBotTileMove($fake_board);

    expect(collect([12, 15]))->toContain($bot_move['space']);
});

it('maximizes its adjacent tiles', function() {
    $this->bootSinglePlayerGame();

    $fake_board = [
        1 => $this->player_2->id,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => $this->player_2->id,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => null,
        13 => null,
        14 => null,
        15 => $this->player_2->id,
        16 => null,
    ];

    $bot_move = $this->game->state()->selectBotTileMove($fake_board);

    expect(collect([15]))->toContain($bot_move['space']);
});

it('prioritizes breaking up player check rather than creating check', function() {
    $this->bootSinglePlayerGame();

    $fake_board = [
        1 => $this->player_2->id,
        2 => $this->player_2->id,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => $this->player_1->id,
        13 => null,
        14 => null,
        15 => $this->player_1->id,
        16 => $this->player_1->id,
    ];

    $bot_move = $this->game->state()->selectBotTileMove($fake_board);

    expect(collect([16]))->toContain($bot_move['space']);
});

it('creates check if it can', function() {
    $this->bootSinglePlayerGame();

    $fake_board = [
        1 => $this->player_2->id,
        2 => null,
        3 => $this->player_2->id,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => $this->player_2->id,
        13 => null,
        14 => null,
        15 => $this->player_2->id,
        16 => null,
    ];

    $bot_move = $this->game->state()->selectBotTileMove($fake_board);

    expect(collect([16]))->toContain($bot_move['space']);
});
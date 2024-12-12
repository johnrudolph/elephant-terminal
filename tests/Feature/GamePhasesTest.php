<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();

    $this->bootMultiplayerGame();
});

it('has the expected phases at the beginning of the game', function () {
    expect($this->game->state()->status)->toBe('active');
});

it('updates turn current player and phase', function () {
    $this->player_1->playTile(1, 'right');

    expect($this->game->state()->current_player_id)->toBe($this->player_1->id);
    expect($this->game->state()->phase)->toBe($this->game->state()::PHASE_MOVE_ELEPHANT);

    $this->player_1->moveElephant(6);

    expect($this->game->state()->current_player_id)->toBe($this->player_2->id);
    expect($this->game->state()->phase)->toBe($this->game->state()::PHASE_PLACE_TILE);

    $this->player_2->playTile(1, 'right');

    expect($this->game->state()->current_player_id)->toBe($this->player_2->id);
    expect($this->game->state()->phase)->toBe($this->game->state()::PHASE_MOVE_ELEPHANT);

    $this->player_2->moveElephant(6);

    expect($this->game->state()->current_player_id)->toBe($this->player_1->id);
    expect($this->game->state()->phase)->toBe($this->game->state()::PHASE_PLACE_TILE);
});

it('skips a player turn if they have no tiles', function () {
    collect(range(1, 4))->each(function ($i) {
        $this->player_1->playTile(1, 'down');
        $this->player_1->moveElephant(6);

        $this->player_2->playTile(3, 'down');
        $this->player_2->moveElephant(6);

        $this->player_1->playTile(4, 'down');
        $this->player_1->moveElephant(6);

        $this->player_2->playTile(3, 'down');
        $this->player_2->moveElephant(6);
    });

    $this->player_2->playTile(3, 'down');
    $this->player_2->moveElephant(6);

    // it is still player 2's turn, even though they just went twice in a row
    expect($this->game->state()->current_player_id)->toBe($this->player_2->id);

    $this->player_2->playTile(1, 'down');
    $this->player_2->moveElephant(6);

    // but now that they pushed one of player 1's tiles, it is player 1's turn.
    expect($this->game->state()->current_player_id)->toBe($this->player_1->id);
});

it('ends the game when a player has a 2x2 grid anywhere', function () {
    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(4, 'down');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(4, 'down');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(13, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(4, 'down');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(13, 'right');

    expect($this->game->state()->status)->toBe('complete');
    expect($this->game->state()->victor_ids)->toBe([(string) $this->player_1->id]);
});

it('can have multiple winners', function () {
    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(4, 'left');
    $this->player_2->moveElephant(10);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(4, 'left');
    $this->player_2->moveElephant(10);

    $this->player_1->playTile(8, 'left');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(10);

    $this->player_1->playTile(8, 'left');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(10);

    $this->player_1->playTile(13, 'right');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(8, 'left');
    $this->player_2->moveElephant(10);

    $this->player_1->playTile(13, 'right');
    $this->player_1->moveElephant(10);

    $this->player_2->playTile(8, 'left');

    expect($this->game->state()->status)->toBe('complete');
    expect(collect($this->game->state()->victor_ids))->toContain((string) $this->player_1->id);
    expect(collect($this->game->state()->victor_ids))->toContain((string) $this->player_2->id);
});

it('ends the game if both players are out of tiles', function () {
    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(13, 'right');
    $this->player_2->moveElephant(2);

    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(2);

    $this->player_2->playTile(5, 'right');
    $this->player_2->moveElephant(2);

    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(2);

    $this->player_2->playTile(5, 'right');
    $this->player_2->moveElephant(2);

    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(2);

    $this->player_2->playTile(5, 'right');
    $this->player_2->moveElephant(2);

    $this->player_1->playTile(9, 'right');
    $this->player_1->moveElephant(2);

    $this->player_2->playTile(5, 'right');

    expect($this->game->state()->status)->toBe('complete');
    expect($this->game->state()->victor_ids)->toBe([]);
});

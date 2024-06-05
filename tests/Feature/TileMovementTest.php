<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();

    $this->bootMultiplayerGame();
});

it('can move a tile', function () {
    $this->player_1->playTile(1, 'right');

    expect($this->game->state()->board[1])->toBe($this->player_1->id);
});

it('can push existing tiles to the adjacent space', function () {
    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(1, 'right');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(1, 'right');

    expect($this->game->state()->board[1])->toBe($this->player_2->id);
    expect($this->game->state()->board[2])->toBe($this->player_1->id);
    expect($this->game->state()->board[3])->toBe($this->player_2->id);
    expect($this->game->state()->board[4])->toBe($this->player_1->id);
});

it('pushes tiles off the board and returns them to their owners hand', function () {
    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(1, 'right');
    $this->player_2->moveElephant(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(1, 'right');
    $this->player_2->moveElephant(6);

    $this->assertEquals(6, $this->game->state()->currentPlayer()->hand);
    $this->assertEquals(6, $this->game->state()->idlePlayer()->hand);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(1, 'right');

    $this->assertEquals(6, $this->game->state()->currentPlayer()->hand);
    $this->assertEquals(6, $this->game->state()->idlePlayer()->hand);
});

it('validates that the space and direction are valid', function () {
    expect(function () {
        $this->player_1->playTile(0, 'right');
    })->toThrow('Invalid space');

    expect(function () {
        $this->player_1->playTile(1, 'foo');
    })->toThrow('Invalid direction');
});

it('does not allow a player to play when it is not their turn', function () {
    expect(function () {
        $this->player_2->playTile(1, 'right');
    })->toThrow('It is not this player '.$this->player_2->id.' turn');

    $this->player_1->playTile(1, 'right');

    expect(function () {
        $this->player_1->playTile(1, 'right');
    })->toThrow('It is time to move the elephant, not play a tile');
});

<?php

use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();

    $this->bootMultiplayerGame();
});

it('allows a player to move the elephant after playing a tile', function () {
    expect($this->game->state()->elephant_position)->toBe(6);

    $this->player_1->playTile(1, 'right');
    $this->player_1->moveElephant(2);

    expect($this->game->state()->elephant_position)->toBe(2);
});

it('blocks movement', function () {
    $this->player_1->playTile(2, 'down');
    $this->player_1->moveElephant(2);

    expect($this->game->state()->slideIsBlockedByElephant(2, 'down'))->toBe(true);

    $this->player_2->playTile(1, 'down');
    $this->player_2->moveElephant(3);

    expect($this->game->state()->slideIsBlockedByElephant(1, 'right'))->toBe(true);

    $this->player_1->playTile(15, 'up');
    $this->player_1->moveElephant(4);

    expect($this->game->state()->slideIsBlockedByElephant(1, 'right'))->toBe(false);

    $this->player_2->playTile(3, 'down');
    $this->player_2->moveElephant(4);

    expect($this->game->state()->slideIsBlockedByElephant(1, 'right'))->toBe(true);

    $this->player_1->playTile(15, 'up');
    $this->player_1->moveElephant(3);

    $this->player_2->playTile(4, 'left');
    $this->player_2->moveElephant(3);

    $this->player_1->playTile(15, 'up');

    expect($this->game->state()->slideIsBlockedByElephant(1, 'right'))->toBe(true);
    expect($this->game->state()->slideIsBlockedByElephant(4, 'left'))->toBe(true);
    expect($this->game->state()->slideIsBlockedByElephant(15, 'up'))->toBe(true);
});

it('does not allow you to slide a tile in a space that is blocked', function () {
    $this->player_1->playTile(5, 'right');
    $this->player_1->moveElephant(6);

    $this->player_2->playTile(5, 'right');
})->throws('Elephant blocks this slide.');

it('does not allow you to move the elephant to an invalid space', function () {
    $this->player_1->playTile(5, 'right');

    expect(function () {
        $this->player_1->moveElephant(16);
    })->toThrow('Elephant cannot reach that space');

    expect(function () {
        $this->player_1->moveElephant(293587);
    })->toThrow('Invalid space');
});

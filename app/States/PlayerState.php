<?php

namespace App\States;

use App\Models\Player;
use Thunk\Verbs\State;
use App\States\GameState;

class PlayerState extends State
{
    public int $game_id;

    public int $user_id;

    public bool $is_host;

    public bool $is_bot;

    public ?string $bot_difficulty = null;

    public int $hand = 8;

    public string $victory_shape;

    public function model(): Player
    {
        return Player::find($this->id);
    }

    public function game(): GameState
    {
        return GameState::load($this->game_id);
    }
}

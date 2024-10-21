<?php

namespace App\States;

use App\Models\Player;
use Thunk\Verbs\State;

class PlayerState extends State
{
    public int $user_id;

    public bool $is_host;

    public bool $is_bot;

    public ?string $bot_difficulty = null;

    public int $hand = 8;

    public function model(): Player
    {
        return Player::find($this->id);
    }
}

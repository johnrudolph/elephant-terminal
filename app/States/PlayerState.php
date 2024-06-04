<?php

namespace App\States;

use Thunk\Verbs\State;

class PlayerState extends State
{
    public int $user_id;

    public bool $is_host;

    public bool $is_bot;

    public ?string $bot_difficulty = null;

    public int $hand;
}

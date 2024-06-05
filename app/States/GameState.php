<?php

namespace App\States;

use App\States\Traits\BoardLogic;
use Thunk\Verbs\State;

class GameState extends State
{
    use BoardLogic;

    public string $status;

    public bool $is_single_player;

    public int $player_1_id;

    public int $player_2_id;

    public int $current_player_id;

    public string $phase;

    public array $victors;

    const PHASE_PLACE_TILE = 'tile';

    const PHASE_MOVE_ELEPHANT = 'move';

    public int $elephant_position = 6;

    public function currentPlayer(): PlayerState
    {
        return PlayerState::load($this->current_player_id);
    }

    public function idlePlayer(): PlayerState
    {
        return $this->current_player_id === $this->player_1_id
            ? PlayerState::load($this->player_2_id)
            : PlayerState::load($this->player_1_id);
    }
}

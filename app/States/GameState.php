<?php

namespace App\States;

use Thunk\Verbs\State;
use App\States\Traits\BoardLogic;
use Illuminate\Support\Collection;

class GameState extends State
{
    use BoardLogic; 
    
    public string $status;

    public bool $is_single_player;

    public int $player_1_id;

    public int $player_2_id;

    public int $current_player_id;

    public string $phase;

    const PHASE_PLACE_TILE = 'tile';

    const PHASE_MOVE_ELEPHANT = 'move';

    public string $elephant_position;

    public Collection $board;

    public function currentPlayer(): PlayerState
    {
        return PlayerState::load($this->current_player_id);
    }
}

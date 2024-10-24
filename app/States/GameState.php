<?php

namespace App\States;

use App\Models\Game;
use App\States\Traits\BoardLogic;
use App\States\Traits\BotLogic;
use Thunk\Verbs\State;

class GameState extends State
{
    use BoardLogic, BotLogic;

    public string $status;

    public bool $is_single_player;

    public int $player_1_id;

    public int $player_2_id;

    public int $current_player_id;

    public string $phase;

    public array $moves;

    public array $victors;

    const PHASE_PLACE_TILE = 'tile';

    const PHASE_MOVE_ELEPHANT = 'move';

    public int $elephant_space = 6;

    public function model(): Game
    {
        return Game::find($this->id);
    }

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

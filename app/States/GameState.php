<?php

namespace App\States;

use App\Models\Game;
use Thunk\Verbs\State;
use App\States\Traits\BotLogic;
use App\States\Traits\BoardLogic;
use Illuminate\Support\Collection;

class GameState extends State
{
    use BoardLogic, BotLogic;

    public string $status;

    public bool $is_single_player;

    public bool $is_ranked;

    public int $player_1_id;

    public int $player_2_id;

    public string $player_1_victory_shape;

    public string $player_2_victory_shape;

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

    public function players(): Collection
    {
        return collect([$this->player_1_id, $this->player_2_id])
            ->map(fn($player_id) => PlayerState::load($player_id));
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

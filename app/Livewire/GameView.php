<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use App\Events\PlayerPlayedTile;
use Livewire\Attributes\Computed;
use App\Events\PlayerMovedElephant;
use Illuminate\Support\Facades\Auth;

class GameView extends Component
{
    public Game $game;

    public array $board;

    public int $elephant_space;

    public bool $is_player_turn;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function player()
    {
        return $this->game->players()->where('user_id', $this->user->id)->first();
    }

    #[Computed]
    public function tiles()
    {
        return collect($this->board)
            ->reject(fn ($space) => $space === null)
            ->toArray();
    }

    public function mount(Game $game)
    {
        $this->game = $game;

        $this->board = $this->game->board;

        $this->elephant_space = $this->game->elephant_space;

        $this->is_player_turn = $this->game->current_player_id === (string) $this->player->id && $this->game->status === 'active';
    }

    public function playTile($direction, $index)
    {
        $space = match($direction) {
            'down' => $index,
            'up' => $index + 12,
            'right' => 1 + ($index - 1) * 4,
            'left' => $index * 4,
        };

        PlayerPlayedTile::fire(
            game_id: $this->game->id,
            space: $space,
            direction: $direction,
            player_id: $this->player->id,
            board_before_slide: $this->board,
        );
    }

    public function moveElephant($space)
    {
        $this->player->moveElephant($space);
    }

    public function render()
    {
        return view('livewire.game-view');
    }
}

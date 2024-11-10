<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use Livewire\Component;
use Thunk\Verbs\Facades\Verbs;
use App\Events\PlayerPlayedTile;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class GameView extends Component
{
    public Game $game;

    public array $board;

    public int $elephant_space;

    public bool $is_player_turn;

    public string $phase;

    public string $game_status;

    public int $player_hand;

    public int $opponent_hand;

    public array $valid_elephant_moves;

    public array $valid_slides;

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
    public function opponent(): Player
    {
        return $this->game->players->firstWhere('user_id', '!=', $this->user->id);
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

        $this->phase = $this->game->phase;

        $this->game_status = $this->game->status;

        $this->player_hand = $this->player->hand;

        $this->opponent_hand = $this->opponent->hand;

        $this->valid_elephant_moves = $this->game->valid_elephant_moves;

        $this->valid_slides = $this->game->valid_slides;
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
        $this->player->moveElephant($space, skip_bot_phase: true);

        Verbs::commit();

        if ($this->opponent->is_bot) {
            sleep(1);
            $this->opponent->playTile();
            Verbs::commit();
            sleep(1);
            $this->opponent->moveElephant();
            Verbs::commit();
        }
    }

    public function getListeners()
    {
        return [
            "echo-private:games.{$this->game->id}:PlayerMovedElephantBroadcast" => 'handleElephantMove',
            "echo-private:games.{$this->game->id}:PlayerPlayedTileBroadcast" => 'handleTilePlayed',
        ];
    }

    public function handleElephantMove($event)
    {
        $move = Move::find($event['move_id']);

        if ($move->player_id === $this->player->id) {
            return;
        }

        $this->game->refresh();

        $this->elephant_space = $move->elephant_after;

        $this->valid_slides = $this->game->valid_slides;

        $this->valid_elephant_moves = $this->game->valid_elephant_moves;

        $this->phase = $this->game->phase;

        $this->game_status = $this->game->status;

        $this->is_player_turn = $this->game->current_player_id === (string) $this->player->id && $this->game->status === 'active';
    }

    public function handleTilePlayed($event)
    {
        $move = Move::find($event['move_id']);

        if ($move->player_id === $this->player->id) {
            return;
        }
        
        unset($this->tiles);
    }

    public function render()
    {
        return view('livewire.game-view');
    }
}

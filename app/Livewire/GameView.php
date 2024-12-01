<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use Livewire\Component;
use Thunk\Verbs\Facades\Verbs;
use App\Events\PlayerPlayedTile;
use App\Events\UserAddedFriend;
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

    public ?int $opponent_hand = 0;

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
    public function opponent(): ?Player
    {
        return $this->game->players->firstWhere('user_id', '!=', $this->user->id);
    }

    #[Computed]
    public function opponent_is_friend()
    {
        return $this->user->friendship_status_with($this->opponent->user);
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

        if (! $this->player || ! $this->opponent) {
            return redirect()->route('games.pre-game-lobby.show', $this->game);
        }

        $this->board = $this->game->board;

        $this->elephant_space = $this->game->elephant_space;

        $this->is_player_turn = $this->game->current_player_id === (string) $this->player->id && $this->game->status === 'active';

        $this->phase = $this->game->phase;

        $this->game_status = $this->game->status;

        $this->player_hand = $this->player->hand;

        $this->opponent_hand = $this->opponent?->hand;

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

        if ($this->opponent->is_bot && $this->game->status === 'active' && $this->opponent->hand > 0) {
            sleep(0.5);
            $this->opponent->playTile();
            Verbs::commit();

            $this->game->refresh();
            $this->game_status = $this->game->status;

            if ($this->game->status === 'active') { 
                sleep(2);
                $this->opponent->moveElephant();
                Verbs::commit();
            }
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

        $direction = match($move->initial_slide['direction']) {
            'down' => 'down',
            'up' => 'up',
            'left' => 'from_right',
            'right' => 'from_left',
        };

        $position = match($move->initial_slide['direction']) {
            'down' => $move->initial_slide['space'] - 1,
            'up' => $move->initial_slide['space'] - 13,
            'right' => ($move->initial_slide['space'] - 1) / 4,
            'left' => ($move->initial_slide['space'] / 4) - 1,
        };

        $this->dispatch('opponent-played-tile', [
            'direction' => $direction,
            'position' => $position,
            'player_id' => (string) $move->player_id
        ]);

        $this->game->refresh();

        $this->valid_slides = $this->game->valid_slides;

        $this->valid_elephant_moves = $this->game->valid_elephant_moves;

        $this->phase = $this->game->phase;

        $this->game_status = $this->game->status;

        $this->is_player_turn = $this->game->current_player_id === (string) $this->player->id && $this->game->status === 'active';
    }

    public function sendFriendRequest()
    {
        UserAddedFriend::fire(
            user_id: $this->user->id,
            friend_id: $this->opponent->user->id,
        );

        Verbs::commit();

        unset($this->opponent_is_friend);
    }

    public function render()
    {
        return view('livewire.game-view');
    }
}

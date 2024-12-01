<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use App\Events\GameCreated;
use App\Events\GameStarted;
use Thunk\Verbs\Facades\Verbs;
use Livewire\Attributes\Computed;

class HomePage extends Component
{
    public bool $is_bot_game = false;

    public bool $is_ranked_game = false;

    public bool $is_friends_only = false;

    #[Computed]
    public function user()
    {
        return auth()->user();
    }

    #[Computed]
    public function friends()
    {
        return $this->user->friends();
    }

    #[Computed]
    public function games()
    {
        return Game::where('status', 'created')
            ->with(['players.user'])
            ->get()
            ->filter(function ($game) {
                return ! $game->is_friends_only
                || $game->players->first()->user->is_friends_with($this->user);
            })
            ->sortByDesc('created_at')
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'player' => $game->players->first()->user->name,
                    'is_friend' => $game->players->first()->user->is_friends_with($this->user),
                ];
            });
    }

    #[Computed]
    public function friend_games()
    {
        return $this->all_upcoming_games->filter(function ($game) {
            return $game->players->first()->user->isFriendsWith($this->user);
        });
    }

    public function newGame()
    {
        $game_id = GameCreated::fire(
            user_id: $this->user->id,
            is_single_player: $this->is_bot_game,
            bot_difficulty: 'hard',
            is_ranked: $this->is_ranked_game,
            is_friends_only: $this->is_friends_only,
        )->game_id;

        if ($this->is_bot_game) {
            GameStarted::fire(game_id: $game_id);
        }

        Verbs::commit();

        return redirect()->route('games.show', $game_id);
    }

    public function render()
    {
        return view('livewire.home-page');
    }
}

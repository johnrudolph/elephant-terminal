<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Player;
use Livewire\Component;
use App\Events\GameStarted;
use App\Events\GameAbandoned;
use App\Events\PlayerCreated;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class PreGameLobby extends Component
{
    public Game $game;

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

    public function getListeners()
    {
        return [
            "echo-private:games.{$this->game->id}:PlayerCreatedBroadcast" => 'handlePlayerCreated',
            "echo-private:games.{$this->game->id}:GameStartedBroadcast" => 'handleGameStarted',
            "echo-private:games.{$this->game->id}:GameAbandonedBroadcast" => 'handleGameAbandoned',
        ];
    }

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->checkIfAbandoned();
    }

    public function checkIfAbandoned()
    {
        if ($this->game->status === 'abandoned') {
            return redirect()->route('dashboard');
        }
    }

    public function handlePlayerCreated($event)
    {
        unset($this->opponent);
    }

    public function handleGameStarted($event)
    {
        return redirect()->route('games.show', $this->game->id);
    }

    public function join()
    {
        PlayerCreated::fire(
            game_id: $this->game->id,
            user_id: $this->user->id,
        );
    }

    public function start()
    {
        GameStarted::fire(game_id: $this->game->id);

        return redirect()->route('games.show', $this->game->id);
    }

    public function leave()
    {
        GameAbandoned::fire(game_id: $this->game->id);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.pre-game-lobby');
    }
}

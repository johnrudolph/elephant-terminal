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
        ];
    }

    public function mount(Game $game)
    {
        $this->game = $game;

        if ($this->game->status === 'abandoned') {
            return redirect()->route('home');
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

    public function render()
    {
        return view('livewire.pre-game-lobby');
    }

    protected $listeners = ['disconnected' => 'handleDisconnect'];

    public function handleDisconnect()
    {
        GameAbandoned::fire(game_id: $this->game->id);
    }
}

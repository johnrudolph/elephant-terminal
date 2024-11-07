<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\GameCreated;
use App\Events\GameStarted;
use Thunk\Verbs\Facades\Verbs;
use Livewire\Attributes\Computed;

class HomePage extends Component
{
    #[Computed]
    public function user()
    {
        return auth()->user();
    }

    public function newGame(?bool $is_bot_game = true)
    {
        $game_id = GameCreated::fire(
            user_id: $this->user->id,
            is_single_player: $is_bot_game,
            bot_difficulty: 'hard',
        )->game_id;

        GameStarted::fire(game_id: $game_id);

        Verbs::commit();

        return redirect()->route('games.show', $game_id);
    }

    public function render()
    {
        return view('livewire.home-page');
    }
}

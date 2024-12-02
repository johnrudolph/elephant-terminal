<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Thunk\Verbs\Event;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameCreated extends Event
{
    #[StateId(GameState::class)]
    public ?int $game_id = null;

    public int $user_id;

    public bool $is_ranked;

    public bool $is_friends_only;

    public bool $is_single_player;

    public ?string $bot_difficulty = 'hard';

    public function apply(GameState $state)
    {
        $state->status = 'created';

        $state->is_single_player = $this->is_single_player;

        $state->victors = [];

        $state->moves = [];

        $state->phase = $state::PHASE_PLACE_TILE;
    }

    public function handle()
    {
        $game = $this->state(GameState::class);

        dump(Game::all()->pluck('id'));

        Game::create([
            'id' => $this->game_id,
            'status' => 'created',
            'board' => $game->board,
            'valid_elephant_moves' => $game->validElephantMoves(),
            'valid_slides' => $game->validSlides(),
            'elephant_space' => $game->elephant_space,
            'phase' => $game->phase,
            'victors' => $game->victors,
            'is_ranked' => $this->is_ranked,
            'is_friends_only' => $this->is_friends_only,
        ]);

//         dd(
// User::find($this->user_id)->games(),
// Game::all()
//         );

        User::find($this->user_id)->games
            ->filter(fn($g) => $g->status === 'created' && $g->id !== $this->game_id)
            ->each(function ($game) {
                $game->status = 'abandoned';
                $game->save();
            });
    }
}

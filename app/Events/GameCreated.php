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

    public ?string $victory_shape = null;

    public function apply(GameState $state)
    {
        $state->status = 'created';

        $state->is_single_player = $this->is_single_player;

        $state->victors = [];

        $state->moves = [];

        $state->phase = $state::PHASE_PLACE_TILE;
    }

    // public function fired()
    // {
    //     if (! $this->victory_shape) {
    //         $this->victory_shape = collect([
    //             'square', 
    //             'line', 
    //             // 'pyramid', 
    //             'el', 
    //             'zig'
    //         ])->random();
    //     }

    //     PlayerCreated::fire(
    //         game_id: $this->game_id,
    //         user_id: $this->user_id,
    //         is_host: true,
    //         is_bot: false,
    //         victory_shape: $this->victory_shape,
    //     );

    //     if ($this->is_single_player) {
    //         PlayerCreated::fire(
    //             game_id: $this->game_id,
    //             // @todo this is yucky
    //             user_id: User::firstWhere('email', 'bot@bot.bot')->id,
    //             is_host: false,
    //             is_bot: true,
    //             bot_difficulty: $this->bot_difficulty,
    //             victory_shape: $this->victory_shape,
    //         );
    //     }
    // }

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

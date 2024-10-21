<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Inertia\Inertia;
use App\States\GameState;
use App\Events\GameCreated;
use App\Events\GameStarted;
use App\Events\PlayerMovedElephant;
use Illuminate\Http\Request;
use Thunk\Verbs\Facades\Verbs;
use App\Events\PlayerPlayedTile;
use App\Events\PlayerPlayedTileBroadcast;
use Illuminate\Support\Facades\Redirect;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $validated = (object) $request->validate([
            'is_bot_game' => 'boolean',
            'bot_difficulty' => 'string|in:easy,normal,hard'
        ]);

        $game_id = GameCreated::fire(
            user_id: auth()->id(),
            is_single_player: $validated->is_bot_game,
            bot_difficulty: $validated->bot_difficulty,
        )->game_id;

        if($validated->is_bot_game) {
            GameStarted::fire(game_id: $game_id);
        }

        Verbs::commit();

        return to_route('games.show', $game_id);
        // return Inertia::location(route('games.show', ['game' => $game_id]));
    }

    public function show(Game $game)
    {
        $state = GameState::load($game->id);

        return Inertia::render('Game', [
            'game' => $game,
            'game_id_string' => (string) $game->id,
            'current_player_id_string' => (string) $game->current_player_id,
            'player_id_string' => (string) $state->player_1_id,
            'opponent_id_string' => (string) $state->player_2_id,
        ]);
    }

    public function play_tile(Request $request)
    {
        dump($request);
        $validated = (object) $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'space' => 'required|int|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16',
            'direction' => 'required|string|in:down,up,right,left'
        ]);
        dump($validated);

        $user_id = auth()->id();

        $player = Game::find($validated->game_id)->players->firstWhere('user_id', $user_id);

        PlayerPlayedTile::fire(
            player_id: $player->id,
            game_id: (int) $validated->game_id,
            space: $validated->space,
            direction: $validated->direction,
        );

        dump('played');

        Verbs::commit();

        PlayerPlayedTileBroadcast::dispatch(Game::find($validated->game_id));
    }

    public function move_elephant(Request $request)
    {
        $validated = (object) $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'space' => 'required|int|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16',
        ]);

        $user_id = auth()->id();

        $player = Game::find($validated->game_id)->players->firstWhere('user_id', $user_id);

        dump($validated->space);

        PlayerMovedElephant::fire(
            player_id: (int) $player->id,
            game_id: (int) $validated->game_id,
            space: (int) $validated->space,
        );

        Verbs::commit();
    }
}

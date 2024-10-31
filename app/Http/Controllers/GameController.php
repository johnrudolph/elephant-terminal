<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Move;
use App\Models\User;
use Inertia\Inertia;
use App\States\GameState;
use App\Events\GameCreated;
use App\Events\GameStarted;
use Illuminate\Http\Request;
use Thunk\Verbs\Facades\Verbs;
use App\Events\PlayerPlayedTile;
use App\Events\PlayerMovedElephant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Events\PlayerPlayedTileBroadcast;
use App\Events\PlayerMovedElephantBroadcast;

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
            'moves' => (array) $state->model()->moves->fresh(),
            'players' => $game->players->map(function($player) {
                return [
                    'id' => (string) $player->id,
                    'user_id' => $player->user_id,
                    'is_bot' => $player->is_bot,
                    'victory_shape' => $player->victory_shape,
                    'hand' => $player->hand,
                    'is_user' => $player->user_id === auth()->id(),
                ];
            }),
        ]);
    }

    public function play_tile(Request $request)
    {
        $validated = (object) $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'space' => 'required|int|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16',
            'direction' => 'required|string|in:down,up,right,left'
        ]);

        $user_id = auth()->id();

        $game = Game::find($validated->game_id);

        $player = $game->players->firstWhere('user_id', $user_id);

        $player->playTile($validated->space, $validated->direction);
        Verbs::commit();

        $move = $player->moves->last();

        PlayerPlayedTileBroadcast::dispatch($game->fresh(), $move);
    }

    public function move_elephant(Request $request)
    {
        $validated = (object) $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'space' => 'required|int|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16',
        ]);

        $user_id = auth()->id();

        $game = Game::find($validated->game_id);

        $player = $game->players->firstWhere('user_id', $user_id);
        $player->moveElephant((int) $validated->space);
        Verbs::commit();

        $move = $player->moves->last();

        PlayerMovedElephantBroadcast::dispatch($game->fresh(), $move);
    }
}

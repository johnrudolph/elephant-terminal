<?php

namespace Tests;

use App\Events\GameCreated;
use App\Events\PlayerCreated;
use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public Game $game;

    public Player $player_1;

    public Player $player_2;

    public function bootMultiplayerGame()
    {
        User::factory()->count(2)->create();

        $game_id = GameCreated::fire(
            user_id: 1
        )->game_id;

        $this->game = Game::find($game_id);

        $this->player_1 = $this->game->players->first();

        $player_2_id = PlayerCreated::fire(
            game_id: $game_id,
            user_id: 2
        )->player_id;

        $this->player_2 = Player::find($player_2_id);
    }

    public function bootSinglePlayerGame()
    {
        User::factory()->create();

        GameCreated::fire(
            user_id: 1,
            is_single_player: true,
            bot_difficulty: 'normal'
        );
    }
}

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

    public function bootSinglePlayerGame(string $bot_difficulty = 'hard')
    {
        User::factory()->create();

        $game_id = GameCreated::fire(
            user_id: 1,
            is_single_player: true,
            bot_difficulty: $bot_difficulty
        )->game_id;

        $this->game = Game::find($game_id);

        $this->player_1 = $this->game->players->first();

        $this->player_2 = $this->game->players->last();
    }

    public function dumpBoard()
    {
        $elephant_position = $this->game->state()->elephant_position;

        $b = collect($this->game->state()->board)
            ->mapWithKeys(function($occupant, $space) use ($elephant_position) {
                if ($occupant === $this->player_1->id) {
                    return $elephant_position === $space
                        ? [$space => '1E'] 
                        : [$space => '1'];
                }

                if ($occupant === $this->player_2->id) {
                    return $elephant_position === $space
                        ? [$space => '2E'] 
                        : [$space => '2'];
                }

                return $elephant_position === $space
                    ? [$space => 'E'] 
                    : [$space => '-'];
            });

        dump(
            $b[1] . '   ' . $b[2] . '   ' . $b[3] . '   ' . $b[4],
            $b[5] . '   ' . $b[6] . '   ' . $b[7] . '   ' . $b[8],
            $b[9] . '   ' . $b[10] . '   ' . $b[11] . '   ' . $b[12],
            $b[13] . '   ' . $b[14] . '   ' . $b[15] . '   ' . $b[16],
        );
    }
}

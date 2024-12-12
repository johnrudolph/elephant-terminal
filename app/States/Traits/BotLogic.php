<?php

namespace App\States\Traits;

trait BotLogic
{
    public function selectBotTileMove(array $board)
    {
        $possible_moves_ranked = collect($this->validSlides($board))
            ->shuffle()
            ->map(function ($slide) use($board) {
                $score = $this->boardScore($this->hypotheticalBoardAfterSlide($slide['space'], $slide['direction'], $board));

                return [
                    'space' => $slide['space'],
                    'direction' => $slide['direction'],
                    'score' => $score,
                ];
            })
            ->sortByDesc('score');

        return $possible_moves_ranked;
    }

    public function selectBotElephantMove()
    {
        // @todo actually score elephant moves
        return collect($this->validElephantMoves())
            ->shuffle()
            ->map(fn ($move) => [
                'space' => $move,
                'score' => 1,
            ])
            ->sortByDesc('score');   
    }

    public function hypotheticalBoardAfterSlide(int $space, string $direction, array $board)
    {
        $hypothetical_board = $board;

        $second_space = $this->slidingPositions($space, $direction)[1];
        $third_space = $this->slidingPositions($space, $direction)[2];
        $fourth_space = $this->slidingPositions($space, $direction)[3];

        if (
            $hypothetical_board[$space]
            && $hypothetical_board[$second_space]
            && $hypothetical_board[$third_space]
        ) {
            $hypothetical_board[$fourth_space] = $board[$third_space];
        }

        if (
            $hypothetical_board[$space]
            && $hypothetical_board[$second_space]
        ) {
            $hypothetical_board[$third_space] = $board[$second_space];
        }

        if ($hypothetical_board[$space]) {
            $hypothetical_board[$second_space] = $board[$space];
        }

        $hypothetical_board[$space] = (string) $this->player_2_id;

        return $hypothetical_board;
    }

    public function boardScore(array $hypothetical_board)
    {
        $score = 0;

        $score += $this->numberOfAdjacentTilesFor($this->player_2_id, $hypothetical_board);

        $score -= $this->numberOfAdjacentTilesFor($this->player_1_id, $hypothetical_board);

        if($this->hypotheticallyHasCheck($this->player_2_id, $hypothetical_board)) {
            $score += 100;
        }

        if($this->hypotheticallyHasCheck($this->player_1_id, $hypothetical_board)) {
            $score -= 200;
        }

        if(collect($this->victors($hypothetical_board))->contains($this->player_1_id)) {
            $score -= 1000;
        }

        if(collect($this->victors($hypothetical_board))->contains($this->player_2_id)) {
            $score += 1000000000;
        }

        if($this->botHypotheticallyRunsOutOfTiles($hypothetical_board)) {
            $score -= 500;
        }

        return $score;
    }

    public function spacesOccupiedBy(string $player_id, array $hypothetical_board)
    {
        return collect($hypothetical_board)
            ->filter(fn ($occupant) => $occupant === $player_id)
            ->keys();
    }

    public function numberOfAdjacentTilesFor(string $player_id, array $hypothetical_board)
    {
        return $this->spacesOccupiedBy($player_id, $hypothetical_board)
            ->map(fn ($space) => collect($this->adjacentSpaces($space))
                ->filter(fn ($adjacent_space) => $hypothetical_board[$adjacent_space] === $player_id)
                ->count()
            )
            ->sum();
    }

    public function botHypotheticallyRunsOutOfTiles(array $hypothetical_board)
    {      
        return collect($hypothetical_board)
            ->filter(fn ($occupant) => $occupant === $this->player_2_id)
            ->count() === 8;
    }

    public function hypotheticallyHasCheck(string $player_id, array $hypothetical_board)
    {
        $player_victory_shape = $player_id === $this->player_1_id
            ? $this->player_1_victory_shape
            : $this->player_2_victory_shape;

        return match($player_victory_shape) {
            'el' => $this->hasTriangleCheck($player_id, $hypothetical_board) || $this->hasLineCheck($player_id, $hypothetical_board),
            'line' => $this->hasLineCheck($player_id, $hypothetical_board),
            'zig' => $this->hasTriangleCheck($player_id, $hypothetical_board),
            'pyramid' => $this->hasTriangleCheck($player_id, $hypothetical_board) || $this->hasLineCheck($player_id, $hypothetical_board),
            'square' => $this->hasTriangleCheck($player_id, $hypothetical_board),
        };
    }

    public function hasTriangleCheck(string $player_id, array $hypothetical_board)
    {
        // @todo modify with "triangle of 3 that I can't block with elephant"

        $every_triangle_check = [
            [1, 2, 5],
            [1, 2, 6],
            [2, 3, 6],
            [2, 3, 7],
            [3, 4, 7],
            [3, 4, 8],
            [9, 13, 14],
            [10, 13, 14],
            [10, 14, 15],
            [11, 14, 15],
            [11, 15, 16],
            [12, 15, 16],
            [1, 5, 6],
            [5, 6, 9],
            [5, 9, 10],
            [9, 10, 13],
            [4, 7, 8],
            [7, 8, 12],
            [8, 11, 12],
            [11, 12, 16],
        ];

        return collect($every_triangle_check)
            ->map(fn ($triangle) =>
                $hypothetical_board[$triangle[0]] === $player_id
                && $hypothetical_board[$triangle[1]] === $player_id
                && $hypothetical_board[$triangle[2]] === $player_id
            )
            ->contains(true);

        // @todo: add opponent has a zigzag of 4 with the ability to push it into place
        // bonus: modify the above with "zigzag of 4 that I can't block with elephant"

        return false;
    }

    public function hasLineCheck(string $player_id, array $hypothetical_board)
    {
        $every_lin_check = [
            [1, 2, 3],
            [2, 3, 4],
            [5, 6, 7],
            [6, 7, 8],
            [9, 10, 11],
            [10, 11, 12],
            [13, 14, 15],
            [14, 15, 16],
            [1, 5, 9],
            [5, 9, 13],
            [2, 6, 10],
            [6, 10, 14],
            [3, 7, 11],
            [7, 11, 15],
            [4, 8, 12],
            [8, 12, 16],
        ];

        return collect($every_lin_check)
            ->map(fn ($line) =>
                $hypothetical_board[$line[0]] === $player_id
                && $hypothetical_board[$line[1]] === $player_id
                && $hypothetical_board[$line[2]] === $player_id
            )
            ->contains(true);

        return false;
    }
}

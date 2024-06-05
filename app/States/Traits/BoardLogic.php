<?php

namespace App\States\Traits;

trait BoardLogic
{
    public array $board = [
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
        8 => null,
        9 => null,
        10 => null,
        11 => null,
        12 => null,
        13 => null,
        14 => null,
        15 => null,
        16 => null,
    ];

    public function adjacentSpaces(int $space)
    {
        $adjacentSpaces = [
            1 => [2, 5],
            2 => [1, 3, 6],
            3 => [2, 4, 7],
            4 => [3, 8],
            5 => [1, 6, 9],
            6 => [2, 5, 7, 10],
            7 => [3, 6, 8, 11],
            8 => [4, 7, 12],
            9 => [5, 10, 13],
            10 => [6, 9, 11, 14],
            11 => [7, 10, 12, 15],
            12 => [8, 11, 16],
            13 => [9, 14],
            14 => [10, 13, 15],
            15 => [11, 14, 16],
            16 => [12, 15],
        ];

        return $adjacentSpaces[$space];
    }

    public function slidingPositions(int $space, string $direction)
    {
        $sliding_positions = [
            1 => [
                'down' => [1, 5, 9, 13],
                'right' => [1, 2, 3, 4],
            ],
            2 => [
                'down' => [2, 6, 10, 14],
            ],
            3 => [
                'down' => [3, 7, 11, 15],
            ],
            4 => [
                'down' => [4, 8, 12, 16],
                'left' => [4, 3, 2, 1],
            ],
            5 => [
                'right' => [5, 6, 7, 8],
            ],
            8 => [
                'left' => [8, 7, 6, 5],
            ],
            9 => [
                'right' => [9, 10, 11, 12],
            ],
            12 => [
                'left' => [12, 11, 10, 9],
            ],
            13 => [
                'right' => [13, 14, 15, 16],
                'up' => [13, 9, 5, 1],
            ],
            14 => [
                'up' => [14, 10, 6, 2],
            ],
            15 => [
                'up' => [15, 11, 7, 3],
            ],
            16 => [
                'up' => [16, 12, 8, 4],
                'left' => [16, 15, 14, 13],
            ],
        ];

        return $sliding_positions[$space][$direction];
    }

    public function slidingPositionOccupants(int $space, string $direction)
    {
        return collect($this->slidingPositions($space, $direction))
            ->map(fn ($position) => $this->board[$position]
            )->toArray();
    }

    public function validSlides()
    {
        $all_possible_slides = [
            ['space' => 1, 'direction' => 'down'],
            ['space' => 2, 'direction' => 'down'],
            ['space' => 3, 'direction' => 'down'],
            ['space' => 4, 'direction' => 'down'],
            ['space' => 1, 'direction' => 'right'],
            ['space' => 5, 'direction' => 'right'],
            ['space' => 9, 'direction' => 'right'],
            ['space' => 13, 'direction' => 'right'],
            ['space' => 4, 'direction' => 'left'],
            ['space' => 8, 'direction' => 'left'],
            ['space' => 12, 'direction' => 'left'],
            ['space' => 16, 'direction' => 'left'],
            ['space' => 13, 'direction' => 'up'],
            ['space' => 14, 'direction' => 'up'],
            ['space' => 15, 'direction' => 'up'],
            ['space' => 16, 'direction' => 'up'],
        ];

        return collect($all_possible_slides)->reject(fn ($slide) => $this->slideIsBlockedByElephant($slide['space'], $slide['direction'])
        )->toArray();
    }

    public function slideIsBlockedByElephant(int $space, string $direction)
    {
        $slidingSpaces = $this->slidingPositions($space, $direction);

        if ($this->elephant_position === $space) {
            return true;
        }

        if (
            $this->board[$space]
            && $this->elephant_position === $slidingSpaces[1]
        ) {
            return true;
        }

        if (
            $this->board[$space]
            && $this->board[$slidingSpaces[1]]
            && $this->elephant_position === $slidingSpaces[2]
        ) {
            return true;
        }

        if (
            $this->board[$space]
            && $this->board[$slidingSpaces[1]]
            && $this->board[$slidingSpaces[2]]
            && $this->elephant_position === $slidingSpaces[3]
        ) {
            return true;
        }

        return false;
    }

    public function validElephantMoves()
    {
        $valid_elephant_moves = $this->adjacentSpaces($this->elephant_position);

        $valid_elephant_moves[] = $this->elephant_position;

        return $valid_elephant_moves;
    }

    public function victor()
    {
        return collect([1, 2, 3, 5, 6, 7, 9, 10, 11])->map(function ($space) {
            if (
                $this->board[$space] !== null
                && $this->board[$space] === $this->board[$space + 1]
                && $this->board[$space] === $this->board[$space + 4]
                && $this->board[$space] === $this->board[$space + 5]
            ) {
                return $this->board[$space];
            }
        })
            ->reject(fn ($victor) => $victor === null)
            ->unique()
            ->values()
            ->toArray();
    }
}

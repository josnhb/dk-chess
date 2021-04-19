<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Entities\Queen;
use Illuminate\Support\Collection;

class QueensController {

    /**
     * Count of queens, starting at 0.
     */
    public const QUEEN_COUNT = 100;

    /**
     * Width of board, starting at 0.
     */
    public const MAX_X = 100;

    /**
     * Height of board, starting at 0.
     */
    public const MAX_Y = 100;

    /**
     * Lowest width coordinate.
     */
    public const MIN_X = 0;

    /**
     * Lowest height coordinate.
     */
    public const MIN_Y = 0;

    /**
     * Collection of queen entities in the field.
     *
     * @var Collection
     */
    public $queens;

    /**
     * Maximum possible number of places for this field.
     *
     * @var int
     */
    private $maxTries;

    /**
     * QueensController constructor.
     */
    public function __construct()
    {
        $this->queens = collect();
        $this->maxTries = (self::MAX_X + 1) * (self::MAX_Y + 1);
    }

    /**
     * Run as many iterations required to solve the Queens problem.
     *
     * @return int[]
     */
    public function solve(): array
    {
        $result = null;
        $tries  = 0;
        while ($result === null) {
            $this->queens = collect();
            $result = $this->tryFindSolution();
            $tries++;
        }

        Storage::disk('local')->put("{$this->fingerPrint()}.txt", 1);

        return [
            'tries'       => $tries,
            'fingerPrint' => $this->fingerPrint(),
        ];
    }

    /**
     * Make a specific unique fingerprint of found solution.
     *
     * @return string
     */
    protected function fingerPrint(): string
    {
        $fingerPrint = '';
        for ($y = QueensController::MAX_Y; $y >= QueensController::MIN_Y; $y--) {
            for ($x = QueensController::MIN_X; $x <= QueensController::MAX_X; $x++) {
                $add = 'X';
                foreach ($this->queens as $queen) {
                    if ($queen->x === $x && $queen->y === $y) {
                        $add .= 'Q';
                    }
                }
                $fingerPrint .= $add;
            }
        }

        return $fingerPrint;
    }

    /**
     * Try to randomly find a solution for the queens problem.
     *
     * @return Collection|null
     */
    protected function tryFindSolution(): ?Collection
    {
        for ($id = 0; $id <= self::QUEEN_COUNT; $id++) {
            [$tryX, $tryY] = $this->generateRandomCoords();
            $tried = collect([$tryX, $tryY]);
            $tries = 1;

            while ($this->isBlocked($tryX, $tryY) === true) {
                if ($tried->count() >= $this->maxTries) {
                    return null;
                }

                [$tryX, $tryY] = $this->generateRandomCoords();

                if ($tried->contains([$tryX, $tryY])) {
                    continue;
                }

                $tried->add([$tryX, $tryY]);
                $tries++;
            }

            $queen = new Queen($id, $tryX, $tryY);
            $this->queens->add($queen);
        }

        return $this->queens;
    }

    /**
     * Generate an array random coords that are valid for this board.
     *
     * @return int[]
     */
    protected function generateRandomCoords(): array
    {
        return [rand(self::MIN_X, self::MAX_X), $tryY = rand(self::MIN_X, self::MAX_Y)];
    }

    /**
     * Determine if given coordinate is blocked by any queens position or attack coordinate in the field.
     *
     * @param $x
     * @param $y
     * @return bool
     */
    private function isBlocked($x, $y): bool
    {
        if ($this->queens->isEmpty()) {
            return false;
        }

        foreach ($this->queens as $queen) {
            if ($queen->blocks->contains([$x, $y])) {
                return true;
            }
        }

        return false;
    }
}

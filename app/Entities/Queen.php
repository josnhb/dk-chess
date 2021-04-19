<?php

namespace App\Entities;

use App\Http\Controllers\QueensController;
use Illuminate\Support\Collection;

class Queen {

    /* @var int */
    public $id;

    /* @var int */
    public $x;

    /* @var int */
    public $y;

    /**
     * Collection containing all the coords attacked or occupied by this queen.
     *
     * @var Collection
     */
    public $blocks;

    /**
     * Queen constructor.
     *
     * @param int $id
     * @param int $x
     * @param int $y
     */
    public function __construct(int $id, int $x, int $y)
    {
        $this->id = $id;
        $this->x  = $x;
        $this->y  = $y;
        $this->blocks = collect();
        $this->calculateBlockedCoordinates();
    }

    /**
     * Calculates all coords this queen can attack.
     *
     * @todo Filter out double entries of own location.
     */
    public function calculateBlockedCoordinates(): void
    {
        $this->canHitHorizontal($this->y);
        $this->canHitVertical($this->x);
        $this->canHitNorthEastSouthWest($this->x, $this->y);
        $this->canHitNorthWestSouthEast($this->x, $this->y);
    }

    /**
     * Calculate all horizontal coords the queen can attack or is on.
     *
     * @param int $y
     */
    protected function canHitHorizontal(int $y): void
    {
        for ($targetX = 0; $targetX <= QueensController::MAX_X; $targetX++) {
            $this->blocks->add([$targetX, $y]);
        }
    }

    /**
     * Calculate all the vertical coords the queen can attack or is on.
     *
     * @param int $x
     */
    protected function canHitVertical(int $x) : void
    {
        for ($targetY = 0; $targetY <= QueensController::MAX_Y; $targetY++) {
            $this->blocks->add([$x, $targetY]);
        }
    }

    /**
     * Calculate all the diagonal NW/SE (\) coords the queen can attack or is on.
     *
     * @param int $x
     * @param int $y
     */
    protected function canHitNorthWestSouthEast(int $x, int $y): void
    {
        $targetY = $y;
        for ($targetX = $x; $targetX >= QueensController::MIN_X; $targetX--) {
            $this->blocks->add([$targetX, $targetY]);
            $targetY--;
            if ($targetY < QueensController::MIN_Y) {
                break;
            }
        }

        $targetY = $y;
        for ($targetX = $x; $targetX <= QueensController::MAX_X; $targetX++) {
            $this->blocks->add([$targetX, $targetY]);
            $targetY++;
            if ($targetY > QueensController::MAX_Y) {
                break;
            }
        }
    }

    /**
     * Calculate all the diagonal NE/SW (/) coords the queen can attack or is on.
     *
     * @param int $x
     * @param int $y
     */
    protected function canHitNorthEastSouthWest(int $x, int $y): void
    {
        $targetY = $y;
        for ($targetX = $x; $targetX >= QueensController::MIN_X; $targetX--) {
            $this->blocks->add([$targetX, $targetY]);
            $targetY++;
            if ($targetY > QueensController::MAX_Y) {
                break;
            }
        }

        $targetY = $y;
        for ($targetX = $x; $targetX <= QueensController::MAX_X; $targetX++) {
            $this->blocks->add([$targetX, $targetY]);
            $targetY--;
            if ($targetY < QueensController::MIN_Y) {
                break;
            }
        }
    }
}

<?php

namespace Houfek\GameOfLife;

/**
 * GameBoard
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class GameBoard
{
    /**
     * Holds the current state of the Game Board.
     *
     * @var array
     */
    private $grid = [];

    /**
     * The number of cells in the Game Board, vertically.
     *
     * @var int
     */
    private $verticalSize = 0;

    /**
     * The number of cells in the Game Board, horizontally.
     *
     * @var int
     */
    private $horizontalSize = 0;

    /**
     * The number (1....n) of the current generation in the simulation.
     *
     * @var int
     */
    private $generationNumber = 0;


    /**
     * @param int $verticalSize
     * @param int $horizontalSize
     */
    public function __construct($verticalSize, $horizontalSize)
    {
        $this->verticalSize   = $verticalSize;
        $this->horizontalSize = $horizontalSize;
    }

    /**
     * Traverse the board.
     *
     * @param Callable $callback
     */
    public function traverse($callback)
    {
        for ($y = 0; $y <= $this->verticalSize; $y++) {
            for ($x = 0; $x <= $this->horizontalSize; $x++) {
                $callback($y, $x);
            }
        }
    }

    /**
     * Get the number of Neighbors that are alive.
     *
     * @param int $y
     * @param int $x
     * @return int
     */
    public function getLivingNeighborCount($y, $x)
    {
        $neighborCount = 0;

        for ($offsetY = -1; $offsetY <= 1; $offsetY++) {
            for ($offsetX = -1; $offsetX <= 1; $offsetX++) {

                // We want to count all 8 neighbors, but not the cell itself,
                if ($offsetY !== 0 || $offsetX !== 0) {
                    $neighborCount += ($this->getWrappedMapNeighborCell($y, $x, $offsetY, $offsetX) ? 1 : 0);
                }

            }
        }
        return $neighborCount;
    }

    /**
     * Get the Neighbor Cell on a wrapped map (a map that wraps around).
     *
     * @param $y
     * @param $x
     * @param $yOffset
     * @param $xOffset
     * @return mixed
     */
    private function getWrappedMapNeighborCell($y, $x, $yOffset, $xOffset)
    {
        $mody = $y + $yOffset;
        $modx = $x + $xOffset;

        if ($modx < 0) {
            $modx = $this->horizontalSize - 1;
        }

        if ($mody < 0) {
            $mody = $this->verticalSize - 1;
        }

        if ($modx > $this->horizontalSize - 1) {
            $modx = 0;
        }

        if ($mody > $this->verticalSize - 1) {
            $mody = 0;
        }

        return $this->grid[$mody][$modx];
    }

    /**
     * Is the cell alive?
     *
     * This method just provides 'semantic sugar' by wrapping getCellValue()
     *
     * @param $y
     * @param $x
     * @return bool
     */
    public function isCellAlive($y, $x)
    {
        return $this->getCellValue($y, $x);
    }

    /**
     * Get Cell Value
     *
     * @param $y
     * @param $x
     * @return bool
     */
    public function getCellValue($y, $x)
    {
        return $this->grid[$y][$x];
    }

    /**
     * Set Cell Value
     *
     * @param $y
     * @param $x
     * @param bool $value
     */
    public function setCellValue($y, $x, $value)
    {
        $this->grid[$y][$x] = $value;
    }

    /**
     * Get vertical size of board/grid (in cells)
     *
     * @return int
     */
    public function getVerticalSize()
    {
        return $this->verticalSize;
    }

    /**
     * Get horizontal size of board/grid (in cells)
     *
     * @return int
     */
    public function getHorizontalSize()
    {
        return $this->horizontalSize;
    }

    /**
     * @return int
     */
    public function getGenerationNumber()
    {
        return $this->generationNumber;
    }

    /**
     * @param int $generationNumber
     *
     * @return $this
     */
    public function setGenerationNumber($generationNumber)
    {
        $this->generationNumber = $generationNumber;
        return $this;
    }

    /**
     * Advance the generation count.
     */
    public function nextGeneration()
    {
        $this->generationNumber++;
    }
}

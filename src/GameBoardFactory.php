<?php

namespace Houfek\GameOfLife;

/**
 * GameBoardFactory
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class GameBoardFactory
{
    /**
     * Create a random board configuration.
     *
     * @param int $verticalSize
     * @param int $horizontalSize
     * @param int $density
     * @return GameBoard
     */
    public function createRandomBoard($verticalSize, $horizontalSize, $density)
    {
        $gameBoard = new GameBoard($verticalSize, $horizontalSize);

        // Traverse the GameBoard, randomly determining whether each cell is
        // "alive" or "dead".
        $gameBoard->traverse(
            function ($y, $x) use ($gameBoard, $density) {
                $gameBoard->setCellValue($y, $x, $this->randomCellValue($density));
            }
        );

        return $gameBoard;
    }

    /**
     * Generate a cell value randomly, with probability specified by the density parameter.
     *
     * @param int $density
     * @return bool
     */
    private function randomCellValue($density)
    {
        $dieRoll   = mt_rand(1, 100); // like rolling a 100-sided die.
        $cellValue = $dieRoll <= $density ? true : false;

        return $cellValue;
    }
}

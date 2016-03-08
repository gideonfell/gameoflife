<?php

namespace Houfek\GameOfLife;

/**
 * GameBoardRenderer
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class GameBoardRenderer
{
    const NUM_NON_GRID_LINES_IN_OUTPUT = 7;
    const ASCII_ESCAPE = 27;
    const ASCII_FOR_ALIVE = 35;
    const ASCII_FOR_DEAD = 39;
    const FOOTER_CHAR = "=";
    const NEWLINE = "\n";
    const LEFT_MARGIN_PAD = "  ";

    /**
     * @var GameRunner
     */
    private $gameRunner;

    /**
     * @param GameRunner $gameRunner
     */
    public function __construct(GameRunner $gameRunner)
    {
        $gameRunner->setGameBoardRenderer($this);

        $this->gameRunner = $gameRunner;
    }

    /**
     * Display the Game Board
     */
    public function displayGameBoard()
    {
        $gameBoard = $this->gameRunner->getGameBoard();

        $this->showGridHeader($gameBoard->getGenerationNumber());

        // Traverse the board object, rendering as we go.
        $gameBoard->traverse(
            function ($y, $x) use ($gameBoard) {

                // We use static variable to "remember" the old Y position so
                // that we know when to drop a newline.
                static $oldY = -1;

                $cellValue = $gameBoard->getCellValue($y, $x);

                // If this should be a new line, Drop a newline character.
                if ($y != $oldY) {
                    echo self::NEWLINE . self::LEFT_MARGIN_PAD;
                    $oldY = $y;
                }

                echo $cellValue ? chr(self::ASCII_FOR_ALIVE) : chr(self::ASCII_FOR_DEAD);
            }
        );

        $this->showGridFooter();
        $this->resetDisplayCursor($gameBoard);
    }


    /**
     * Reset the display cursor.
     *
     * We do this so that we can "paint over" the grid from the prior generation,
     * to "animate" the board across generations.
     *
     * @param GameBoard $gameBoard
     */
    private function resetDisplayCursor(GameBoard $gameBoard)
    {
        $numLinesUp = $gameBoard->getVerticalSize() + self::NUM_NON_GRID_LINES_IN_OUTPUT;
        $controlCodeToResetCursor = chr(self::ASCII_ESCAPE) . "[" . $numLinesUp . "A";

        echo $controlCodeToResetCursor;
    }

    /**
     * Show the Grid Header (which reports the Generation Number).
     *
     * @param $generationNumber
     */
    private function showGridHeader($generationNumber)
    {
        $this->showGridBorder();
        echo "    Generation: " . $generationNumber;
        $this->showGridBorder();
    }

    private function showGridFooter()
    {
        $this->showGridBorder();
    }

    /**
     * Show the Grid Footer.
     */
    private function showGridBorder()
    {
        echo self::NEWLINE
            . str_repeat(self::FOOTER_CHAR, $this->getHeaderFooterWidth())
            . self::NEWLINE;
    }

    /**
     * Get the width of the rendered header and footer.
     *
     * @return float
     */
    private function getHeaderFooterWidth()
    {
        $width = round(
            $this->gameRunner->getGameBoard()->getHorizontalSize() * 1.1
        );

        return $width;
    }
}

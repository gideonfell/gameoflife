<?php

namespace Houfek\GameOfLife;

/**
 * GameRunner
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class GameRunner
{
    /**
     * @var GameBoard
     */
    private $gameBoard;

    /**
     * @var GameBoardRenderer
     */
    private $gameBoardRenderer;

    /**
     * @param GameBoard $gameBoard
     */
    public function __construct(GameBoard $gameBoard)
    {
        $this->gameBoard = $gameBoard;
    }

    /**
     * Run the simulation.
     *
     * @param int $numGenerations
     * @param int $sleepTime
     */
    public function run($numGenerations, $sleepTime)
    {
        $this->invokeRenderer();

        for ($i = 1; $i<= $numGenerations; $i++) {
            sleep($sleepTime);

            $this->oneGameRound();
            $this->invokeRenderer();
        }
    }

    /**
     * Execute one Game Round (one generation).
     */
    public function oneGameRound()
    {
        $this->gameBoard->nextGeneration();

        $oldGameBoard = $this->gameBoard;
        $newGameBoard = clone $oldGameBoard;

        $this->gameBoard->traverse(
            function ($y, $x) use ($oldGameBoard, $newGameBoard) {
                $newGameBoard->setCellValue(
                    $y,
                    $x,
                    $this->neighborDerivedCellValue($oldGameBoard, $y, $x)
                );
            }
        );

        $this->gameBoard = $newGameBoard;
    }

    /**
     * Invoke the renderer.
     *
     * (If one has been attached; otherwise we will happily run in a headless
     * mode.)
     */
    public function invokeRenderer()
    {
        if ($this->gameBoardRenderer) {
            $this->gameBoardRenderer->displayGameBoard();
        }
    }

    /**
     * Get a new cell value derived from the values of the cell's neighbors
     *
     * (This enforces the rules of Conway's cellular automata.)
     *
     * @param GameBoard $gameBoard
     * @param $y
     * @param $x
     * @return bool
     */
    private function neighborDerivedCellValue(GameBoard $gameBoard, $y, $x)
    {
        $neighborCount = $gameBoard->getLivingNeighborCount($y, $x);

        $isAlive = $gameBoard->isCellAlive($y, $x);

        // If a live cell has two or three neighbors, it lives.
        if ($isAlive && ($neighborCount === 2 || $neighborCount === 3)) {
            return true;
        }

        // If a dead cell has exactly three neighbors, it becomes alive.
        if (!$isAlive && $neighborCount === 3) {
            return true;
        }

        // Otherwise the cell becomes, or stays, dead.
        return false;
    }

    /**
     * @return GameBoard
     */
    public function getGameBoard()
    {
        return $this->gameBoard;
    }

    /**
     * @param GameBoard $gameBoard
     *
     * @return $this
     */
    public function setGameBoard($gameBoard)
    {
        $this->gameBoard = $gameBoard;
        return $this;
    }

    /**
     * @param GameBoardRenderer $gameBoardRenderer
     */
    public function setGameBoardRenderer(GameBoardRenderer $gameBoardRenderer)
    {
        $this->gameBoardRenderer = $gameBoardRenderer;
    }
}

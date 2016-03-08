<?php
/**
 * gameoflife.php
 *
 * PHP CLI implementation of Conway's Game of Life (https://en.wikipedia.org/wiki/Conway's_Game_of_Life)
 *
 * @author Thomas Houfek <thomas
 */

require_once('vendor/autoload.php');

use Houfek\GameOfLife\GameBoardFactory;
use Houfek\GameOfLife\GameBoardRenderer;
use Houfek\GameOfLife\GameRunner;

const GRID_SIZE_HORIZONTAL = 'horizontalSize';
const GRID_SIZE_VERTICAL   = 'verticalSize';
const GRID_INITIAL_DENSITY = 'initialDensity';
const NUM_GENERATIONS      = 'numGenerations';
const SLEEP_TIME           = 'sleepTime';

const DEFAULT_GRID_SIZE_HORIZONTAL = 60;
const DEFAULT_GRID_SIZE_VERTICAL   = 30;
const DEFAULT_GRID_INITIAL_DENSITY = 10;
const DEFAULT_NUM_GENERATIONS      = 1000;
const DEFAULT_SLEEP_TIME           = 0;

const MESSAGE_USAGE = "
USAGE:
\t%% php gameoflife.php <numGenerations> <initialDensity> <horizontalSize> <verticalSize> <sleepTime>\n
\tnumGenerations specifies how many iterations will be played out.  (Default: %s)\n
\tinitialDensity is the percent chance that each cell starts out alive.  (Default: %s)\n
\thorizontalSize is number of cells in the grid, horizontally. (Default: %s)\n
\tverticalSize is number of cells in the grid, vertically. (Default: %s)\n
\tsleepTime is the number of seconds to pause between generations (Default: %s)\n
EXAMPLE:\n
 \t%% php gameoflife.php 500 50 60 30 0
";

/**
 * Show the usage of this script
 */
function showUsage()
{
    printf(
        MESSAGE_USAGE,
        DEFAULT_NUM_GENERATIONS,
        DEFAULT_GRID_INITIAL_DENSITY,
        DEFAULT_GRID_SIZE_HORIZONTAL,
        DEFAULT_GRID_SIZE_VERTICAL,
        DEFAULT_SLEEP_TIME
    );
}

/**
 * Validate script parameters.
 *
 * @param array $param
 */
function validateParameters(array $param)
{
// All of our parameters should be positive integers.
    foreach (array_keys($param) as $paramKey) {
        $paramValue = $param[$paramKey];
        if (!is_numeric($paramValue) || is_float($paramValue) || $paramValue < 0) {
            showUsage();
            exit;
        }
    }

    // Grid dimensions cannot be less than one cell in size.
    foreach ([GRID_SIZE_HORIZONTAL, GRID_SIZE_VERTICAL] as $paramKey) {
        if ($param[$paramKey] < 1) {
            showUsage();
            exit;
        }
    }

    // Density must be between 0 and 100.  (Under the hood, it is the percent
    // probability of a cell being alive at the start of the Game.)
    if ($param[GRID_INITIAL_DENSITY] < 1 || $param[GRID_INITIAL_DENSITY] > 100 ) {
        showUsage();
        exit;
    }
}

// Process input parameters.
$param = [];
$param[NUM_GENERATIONS]      = isset($argv[1]) ? $argv[1] : DEFAULT_NUM_GENERATIONS;
$param[GRID_INITIAL_DENSITY] = isset($argv[2]) ? $argv[2] : DEFAULT_GRID_INITIAL_DENSITY;
$param[GRID_SIZE_HORIZONTAL] = isset($argv[3]) ? $argv[3] : DEFAULT_GRID_SIZE_HORIZONTAL;
$param[GRID_SIZE_VERTICAL]   = isset($argv[4]) ? $argv[4] : DEFAULT_GRID_SIZE_VERTICAL;
$param[SLEEP_TIME]           = isset($argv[5]) ? $argv[5] : DEFAULT_SLEEP_TIME;

validateParameters($param);


// Create the Game Board.
$gameBoardFactory = new GameBoardFactory();
$gameBoard = $gameBoardFactory->createRandomBoard(
    $param[GRID_SIZE_VERTICAL],
    $param[GRID_SIZE_HORIZONTAL],
    $param[GRID_INITIAL_DENSITY]
);

// Create the GameRunner and the GameBoardRenderer.
$gameRunner = new GameRunner($gameBoard);
$gameBoardRenderer = new GameBoardRenderer($gameRunner);

// Run the Game.
$gameRunner->run($param[NUM_GENERATIONS], $param[SLEEP_TIME]);


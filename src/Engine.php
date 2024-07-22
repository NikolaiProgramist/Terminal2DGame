<?php

namespace Experiments\Engine;

require_once __DIR__ . '/../vendor/autoload.php';

use function cli\line;
use function cli\input;
use function Experiments\Objects\getObject;
use function Experiments\Objects\getRandomObject;
use function Experiments\Objects\objectsBlocking;
use function Experiments\Objects\objectsUse;
use function Experiments\UsePlayer\useBonus;
use function Experiments\Game\saveGame;
use function Experiments\Game\loadGame;
use function Experiments\Game\pagerFromFile;

$player = '♀';
$bonusesReceived = '';

$errorAction = '';

$mapRowSize = 8;
$mapColSize = 16;

$map = [];

$runCell = '▦';

if ($argv[1] === 'load') {
    $saveName = $argv[2];
    $data = loadGame($saveName);

    $map = $data['map'];
    $bonusesReceived = $data['bonusesReceived'];
} else {
    generateMap();
    randomSpawnPlayer();
}

while (true) {
    $handle = popen('clear', 'w');
    pclose($handle);

    line(render());

    line("円 {$bonusesReceived}");

    line(str_repeat('-', $mapColSize));
    line('⌨ wasd | ⊞ e | ? help');

    line("\n{$errorAction}");

    $errorAction = '';

    actionPlayer(input());
}

function generateMap(): void
{
    global $map;
    global $mapRowSize;
    global $mapColSize;

    for ($i = 0; $i < $mapRowSize; $i++) {
        $map[$i] = [];

        for ($j = 0; $j < $mapColSize; $j++) {
            if ($i === 0 || $j === 0 || $i === $mapRowSize - 1 || $j === $mapColSize - 1) {
                $map[$i][] = getObject('wall');
            } else {
                $map[$i][] = getRandomObject();
            }
        }
    }
}

function render(): string
{
    global $map;
    global $mapRowSize;

    $stringMap = '';

    for ($i = 0; $i < $mapRowSize; $i++) {
        $stringMap .= implode('', $map[$i]) . "\n";
    }

    return $stringMap;
}

function randomSpawnPlayer(): void
{
    global $player;
    global $map;
    global $mapRowSize;
    global $mapColSize;

    $isSpawnPlayer = false;

    while (!$isSpawnPlayer) {
        $randomRow = rand(0, $mapRowSize - 1);
        $randomCol = rand(0, $mapColSize - 1);

        if (objectsBlocking($map[$randomRow][$randomCol])) {
            $map[$randomRow][$randomCol] = $player;
            $isSpawnPlayer = true;
        }
    }
}

function actionPlayer(string $action): void
{
    global $map;
    global $bonusesReceived;
    global $mapColSize;

    $command = explode(' ', $action);

    if (count($command) < 2) {
        if ($action === 'help') {
            pagerFromFile('help.txt', $mapColSize, $bonusesReceived);
        }

        if ($action === 'lor') {
            pagerFromFile('lor.txt', $mapColSize, $bonusesReceived);
        }

        if ($action === 'd' || $action === 'a' || $action === 'w' || $action === 's') {
            runPlayer($action);
        }

        if ($action === 'e') {
            usePlayer();
        }

        if ($action === 'exit') {
            exit;
        }
    }

    if (count($command) > 1) {
        if ($command[0] === 'save') {
            $saveName = $command[1];

            saveGame($saveName, $map, $bonusesReceived);
        }
    }
}

function runPlayer(string $action): void
{
    global $player;
    global $map;
    global $runCell;
    global $errorAction;

    $coordsPlayer = findPlayer();

    if ($action === 'd') {
        $coordsRun = $map[$coordsPlayer['row']][$coordsPlayer['col'] + 1];

        if (objectsBlocking($coordsRun)) {
            $map[$coordsPlayer['row']][$coordsPlayer['col']] = $runCell;
            $runCell = $map[$coordsPlayer['row']][$coordsPlayer['col'] + 1];
            $map[$coordsPlayer['row']][$coordsPlayer['col'] + 1] = $player;
        } else {
            $errorAction = 'The path is blocked ✖';
        }
    }

    if ($action === 'a') {
        $coordsRun = $map[$coordsPlayer['row']][$coordsPlayer['col'] - 1];

        if (objectsBlocking($coordsRun)) {
            $map[$coordsPlayer['row']][$coordsPlayer['col']] = $runCell;
            $runCell = $map[$coordsPlayer['row']][$coordsPlayer['col'] - 1];
            $map[$coordsPlayer['row']][$coordsPlayer['col'] - 1] = $player;
        } else {
            $errorAction = 'The path is blocked ✖';
        }
    }

    if ($action === 'w') {
        $coordsRun = $map[$coordsPlayer['row'] - 1][$coordsPlayer['col']];

        if (objectsBlocking($coordsRun)) {
            $map[$coordsPlayer['row']][$coordsPlayer['col']] = $runCell;
            $runCell = $map[$coordsPlayer['row'] - 1][$coordsPlayer['col']];
            $map[$coordsPlayer['row'] - 1][$coordsPlayer['col']] = $player;
        } else {
            $errorAction = 'The path is blocked ✖';
        }
    }

    if ($action === 's') {
        $coordsRun = $map[$coordsPlayer['row'] + 1][$coordsPlayer['col']];

        if (objectsBlocking($coordsRun)) {
            $map[$coordsPlayer['row']][$coordsPlayer['col']] = $runCell;
            $runCell = $map[$coordsPlayer['row'] + 1][$coordsPlayer['col']];
            $map[$coordsPlayer['row'] + 1][$coordsPlayer['col']] = $player;
        } else {
            $errorAction = 'The path is blocked ✖';
        }
    }
}

function usePlayer(): void
{
    global $runCell;
    global $bonusesReceived;
    global $errorAction;

    if (objectsUse($runCell)) {
        $bonusesReceived .= useBonus($runCell);
        $runCell = '▦';
    } else {
        $errorAction = 'This cannot be used ✖';
    }
}

function shouldPlayerRun(array $coordsPlayer): bool
{
    global $map;

    $cell = $map[$coordsPlayer['row']][$coordsPlayer['col']];

    return objectsBlocking($cell);
}

function findPlayer(): array
{
    global $player;
    global $map;

    foreach ($map as $key => $row) {
        $col = array_search($player, $row, true);

        if ($col !== false) {
            return ['row' => $key, 'col' => $col];
        }
    }
}
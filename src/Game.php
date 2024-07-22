<?php

namespace Experiments\Game;

require_once __DIR__ . '/../vendor/autoload.php';

use function cli\line;
use function cli\input;
use function Experiments\Engine\render;
use function Experiments\Engine\actionPlayer;

function saveGame(string $saveName, array $map, string $bonusesReceived): void
{
    $mapSave = '';

    foreach ($map as $row) {
        $mapSave .= implode('', $row) . "\n";
    }

    mkdir("../saves/{$saveName}");

    $fpMap = fopen("../saves/{$saveName}/map.txt", "w");
    fwrite($fpMap, $mapSave);
    fclose($fpMap);

    $fpBonusesReceived = fopen("../saves/{$saveName}/bonusesReceived.txt", "w");
    fwrite($fpBonusesReceived, $bonusesReceived);
    fclose($fpBonusesReceived);
}

function loadGame(string $saveName): array
{
    $map = [];

    if (!file_exists("../saves/{$saveName}")) {
        line('Save doesn\'t exists!');
        exit;
    }

    $mapSave = file("../saves/{$saveName}/map.txt", FILE_IGNORE_NEW_LINES);

    for ($i = 0; $i < count($mapSave); $i++) {
        $map[] = mb_str_split($mapSave[$i]);
    }

    $bonusesReceived = file("../saves/{$saveName}/bonusesReceived.txt");

    return ['map' => $map, 'bonusesReceived' => $bonusesReceived[0]];
}

function pagerFromFile(string $fileName, int $mapColSize, string $bonusesReceived): void
{
    $handle = popen('clear', 'w');
    pclose($handle);

    line(implode('', file("public/{$fileName}")));

    line("\n");

    line(str_repeat('-', $mapColSize));
    line('← Press any key to exit');

    line("\n");

    input();

    $handle = popen('clear', 'w');
    pclose($handle);

    line(render());

    line("円 {$bonusesReceived}");

    line(str_repeat('-', $mapColSize));
    line('⌨ wasd | ⊞ e | ? help');

    line("\n");

    actionPlayer(input());
}
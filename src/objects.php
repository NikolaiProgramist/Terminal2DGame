<?php

namespace Experiments\Objects;

require_once __DIR__ . '/../vendor/autoload.php';

function getObject(string $object): string
{
    $list = [
        'dirt' => '▦',
        'pine' => '▲',
        'flower' => '✿',
        'wall' => '▣',
        'heart' => '❤'
    ];

    return $list[$object];
}

function getRandomObject(): string
{
    $list = [
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▦',
        '▲',
        '▲',
        '▲',
        '▲',
        '▲',
        '▲',
        '✿',
        '✿',
        '✿',
        '✿',
        '❤',
        '❤',
        '❤',
        '✉'
    ];

    return $list[rand(0, count($list) - 1)];
}

function objectsBlocking(string $cell): bool
{
    $list = [
        '▣',
        '▲'
    ];

    if (in_array($cell, $list, true)) {
        return false;
    }

    return true;
}

function objectsUse(string $cell): bool
{
    $list = [
        '❤',
        '✿',
        '✉'
    ];

    return in_array($cell, $list, true);
}
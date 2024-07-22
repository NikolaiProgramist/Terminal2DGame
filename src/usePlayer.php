<?php

namespace Experiments\UsePlayer;

require_once __DIR__ . '/../vendor/autoload.php';

function useBonus(string $bonus): string
{
    if ($bonus === '❤') {
        return '❤';
    }

    if ($bonus === '✿') {
        return '✿';
    }

    if ($bonus === '✉') {
        return '✉';
    }
}
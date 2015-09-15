<?php

/**
 * CLI Menu POC
 */

ini_set('display_errors', 1);

$container = require_once __DIR__ . '/app/bootstrap.php';

$exerciseClasses = $container->get('exercises');

$exercises = [];
foreach ($exerciseClasses as $className) {
    $exercises[] = $container->get($className)->getName();
}

$width    = 100;//$maxlen = max(array_map('strlen', $exercises));
$padding  = 3;
$margin   = 2;
$selected = 0;

/**
 * @param $text
 * @param bool|false $selected
 */
$drawRow = function ($text, $selected = false) use ($width, $padding, $margin) {

    $paddingRight = $width - ($padding * 2) - strlen($text);

    $colour = $selected
        ? sprintf("%c[0;34m%c[47m", 27, 27)
        : sprintf("%c[1;37m%c[44m", 27, 27);

    echo sprintf(
        '%s%s%s%s%s%c[0m%s',
        str_repeat(' ', $margin),
        $colour,
        str_repeat(' ', $padding),
        $text,
        str_repeat(' ', $paddingRight),
        27,
        str_repeat(' ', $margin)
    );

    echo "\n";
};

$drawLine = function () use ($drawRow, $width, $padding, $margin) {
    $length = $width - ($padding*2) - ($margin*2);
    $drawRow(str_repeat('-', $length));
};

$draw = function ($selected) use ($exercises, $drawRow, $drawLine) {

    echo sprintf('%c[H', 27);
    echo "\n";
    echo "\n";

    $drawRow("");
    $drawRow("WELCOME TO THE PHP WORKSHOP");
    $drawLine();

    array_map(function($exercise, $index) use ($drawRow, $selected) {
        $drawRow(">> " . $exercise, $index === $selected);
    }, $exercises, array_keys($exercises));

    $drawLine();
    $drawRow("");

    echo "\n";
};

echo sprintf('%c[1J', 27);
$draw($selected);

system("stty -icanon");
while ($in = fread(STDIN, 4)) {

    // UP
    if ($in === sprintf("%c[A", 27)) {
        if (array_key_exists($selected-1, $exercises)) {
            $selected = $selected-1;
        }
    }
    // DOWN
    if ($in === sprintf("%c[B", 27)) {
        if (array_key_exists($selected+1, $exercises)) {
            $selected = $selected+1;
        }
    }

    // ENTER
    if ($in === "\n" || $in === ' ') {
        echo sprintf("You selected %s \n", $exercises[$selected]);
    }
    $draw($selected);
}


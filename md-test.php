<?php

require_once 'vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\HtmlRenderer;


function inlineText(array $children) {
    return implode(
        "",
        array_map(
            function (\League\CommonMark\Inline\Element\Text $text) {
                return $text->getContent();
            },
            $children
        )
    );
};

$c = new \Colors\Color();

$env = \League\CommonMark\Environment::createCommonMarkEnvironment();

$htmlRenderer = new HtmlRenderer($env);
$docParser = new \League\CommonMark\DocParser($env);

$ast = $docParser->parse(file_get_contents('res/problems/baby-steps/problem.md'));

$children = $ast->children();

$paragraph = $children[0];

$lines = inlineText($paragraph->children());

echo $c($lines . "\n");

$width = exec('tput cols');

$separator = $children[1];
echo $c(str_repeat('-', $width))->yellow();

$heading = $children[2];
$hints = inlineText($heading->children());

echo $c($hints)->bold()->light_blue() . "\n";

var_dump('');


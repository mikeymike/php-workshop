<?php

require_once 'vendor/autoload.php';

$code = '
<?php
    echo "hello";
    $lol = "whut";
    var_dump($lol);
    sprintf("%s lol", "lol");

    $haha = function ($what) {
        return $what;
    };

    if ($haha) {

    } elseif($haha) {

    } else {

    }
';

use Colors\Color;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpWorkshop\PhpWorkshop\ColorsAdapter;
use PhpWorkshop\PhpWorkshop\Colours;
use PhpWorkshop\PhpWorkshop\SyntaxHighlighterConfig;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

try {
    $stmts = $parser->parse($code);
    // $stmts is an array of statement nodes
} catch (Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}

$prettyPrinter = new \PhpWorkshop\PhpWorkshop\SyntaxHighlighter(
    new SyntaxHighlighterConfig,
    new ColorsAdapter(new Color)
);

echo $prettyPrinter->prettyPrint($stmts);
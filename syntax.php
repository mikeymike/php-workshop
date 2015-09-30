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

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

try {
    $stmts = $parser->parse($code);
    // $stmts is an array of statement nodes
} catch (Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}

$prettyPrinter = new \PhpWorkshop\PhpWorkshop\SyntaxHighlighter(new \Colors\Color);

echo $prettyPrinter->prettyPrint($stmts);
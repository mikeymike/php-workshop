<?php

require_once 'vendor/autoload.php';

use League\CommonMark\DocParser;
use League\CommonMark\Environment;

$c = new \Colors\Color();
$docParser = new DocParser(Environment::createCommonMarkEnvironment());
$ast = $docParser->parse(file_get_contents('res/problems/baby-steps/problem.md'));
$children = $ast->children();

$cliRender = new \PhpWorkshop\PhpWorkshop\Md\CliRenderer($c);
echo $cliRender->renderBlock($ast);

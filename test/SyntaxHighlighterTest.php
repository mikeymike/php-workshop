<?php

namespace PhpWorkshop\PhpWorkshopTest;

use PhpParser\Error;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpWorkshop\PhpWorkshop\ColourAdapterInterface;
use PhpWorkshop\PhpWorkshop\SyntaxHighlighter;
use PhpWorkshop\PhpWorkshop\SyntaxHighlighterConfig;

/**
 * Class SyntaxHighlighterTest
 * @package PhpWorkshop\PhpWorkshopTest
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class SyntaxHighlighterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestPrettyPrint
     * @covers \PhpWorkshop\PhpWorkshop\SyntaxHighlighter<extended>
     */
    public function testPrettyPrint($name, $code, $expected, $mode)
    {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $expected, $mode);
    }

    public function provideTestPrettyPrint()
    {
        return $this->getTests(__DIR__ . '/../vendor/nikic/php-parser/test/code/prettyPrinter', 'test');
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     * @covers \PhpWorkshop\PhpWorkshop\SyntaxHighlighter<extended>
     */
    public function testPrettyPrintFile($name, $code, $expected, $mode) {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $expected, $mode);
    }

    public function provideTestPrettyPrintFile() {
        return $this->getTests(__DIR__ . '/../vendor/nikic/php-parser/test/code/prettyPrinter', 'file-test');
    }

    public function testPrettyPrintExpr() {
        $prettyPrinter = $this->getPrinter();
        $expr = new Expr\BinaryOp\Mul(
            new Expr\BinaryOp\Plus(new Expr\Variable('a'), new Expr\Variable('b')),
            new Expr\Variable('c')
        );
        $this->assertEquals('($a + $b) * $c', $prettyPrinter->prettyPrintExpr($expr));

        $expr = new Expr\Closure(array(
            'stmts' => array(new Stmt\Return_(new String_("a\nb")))
        ));
        $this->assertEquals("function () {\n    return 'a\nb';\n}", $prettyPrinter->prettyPrintExpr($expr));
    }

    protected function doTestPrettyPrintMethod($method, $name, $code, $expected, $modeLine) {
        $lexer = new Lexer\Emulative;
        $parser5 = new Parser\Php5($lexer);
        $parser7 = new Parser\Php7($lexer);

        list($version, $options) = $this->parseModeLine($modeLine);
        $prettyPrinter = $this->getPrinter($options);

        try {
            $output5 = $this->canonicalize($prettyPrinter->$method($parser5->parse($code)));
        } catch (Error $e) {
            $output5 = null;
            var_dump($e->getMessage());
            //$this->assertEquals('php7', $version);
        }

        try {
            $output7 = $this->canonicalize($prettyPrinter->$method($parser7->parse($code)));
        } catch (Error $e) {
            $output7 = null;
            $this->assertEquals('php5', $version);
        }

        if ('php5' === $version) {
            $this->assertSame($expected, $output5, $name);
            $this->assertNotSame($expected, $output7, $name);
        } else if ('php7' === $version) {
            $this->assertSame($expected, $output7, $name);
            $this->assertNotSame($expected, $output5, $name);
        } else {
            $this->assertSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        }
    }

    protected function getTests($directory, $fileExtension) {
        $it = new \RecursiveDirectoryIterator($directory);
        $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new \RegexIterator($it, '(\.' . preg_quote($fileExtension) . '$)');

        $tests = array();
        foreach ($it as $file) {
            $fileName = realpath($file->getPathname());
            $fileContents = file_get_contents($fileName);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace_callback(
                '/@@\{(.*?)\}@@/',
                function($matches) {
                    return eval('return ' . $matches[1] . ';');
                },
                $fileContents
            );

            // parse sections
            $parts = array_map('trim', explode('-----', $fileContents));

            // first part is the name
            $name = array_shift($parts) . ' (' . $fileName . ')';
            $shortName = basename($fileName, '.test');

            // multiple sections possible with always two forming a pair
            $chunks = array_chunk($parts, 2);
            foreach ($chunks as $i => $chunk) {
                $dataSetName = $shortName . (count($chunks) > 1 ? '#' . $i : '');
                list($expected, $mode) = $this->extractMode($this->canonicalize($chunk[1]));
                $tests[$dataSetName] = array($name, $chunk[0], $expected, $mode);
            }
        }

        return $tests;
    }

    private function extractMode($expected) {
        $firstNewLine = strpos($expected, "\n");
        if (false === $firstNewLine) {
            $firstNewLine = strlen($expected);
        }

        $firstLine = substr($expected, 0, $firstNewLine);
        if (0 !== strpos($firstLine, '!!')) {
            return [$expected, null];
        }

        $expected = (string) substr($expected, $firstNewLine + 1);
        return [$expected, substr($firstLine, 2)];
    }
    private function parseModeLine($modeLine) {
        $parts = explode(' ', $modeLine, 2);
        $version = isset($parts[0]) ? $parts[0] : 'both';
        $options = isset($parts[1]) ? json_decode($parts[1], true) : [];
        return [$version, $options];
    }

    protected function canonicalize($str) {
        // trim from both sides
        $str = trim($str);

        // normalize EOL to \n
        $str = str_replace(array("\r\n", "\r"), "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }

    private function getPrinter(array $options = [])
    {
        $colourAdapter = $this->getMock(ColourAdapterInterface::class);
        $colourAdapter
            ->expects($this->any())
            ->method('colour')
            ->will($this->returnCallback(function ($string) {
                return $string;
            }));

        return new SyntaxHighlighter(new SyntaxHighlighterConfig, $colourAdapter, $options);
    }
}

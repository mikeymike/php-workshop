<?php


namespace PhpWorkshop\PhpWorkshopTest;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use PhpWorkshop\PhpWorkshop\Check\CheckInterface;
use PhpWorkshop\PhpWorkshop\Check\StdOutCheck;
use PhpWorkshop\PhpWorkshop\Exercise\ExerciseInterface;
use PhpWorkshop\PhpWorkshop\Exercise\HelloWorld;
use PhpWorkshop\PhpWorkshop\ExerciseCheck\StdOutExerciseCheck;
use PhpWorkshop\PhpWorkshop\ExerciseRunner;
use PhpWorkshop\PhpWorkshop\Result\Failure;
use PhpWorkshop\PhpWorkshop\Result\Success;
use PhpWorkshop\PhpWorkshop\ResultAggregator;
use stdClass;

/**
 * Class ExerciseRunnerTest
 * @package PhpWorkshop\PhpWorkshopTest
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ExerciseRunnerTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterExerciseWithNonStringNonNullThrowsException()
    {
        $runner = new ExerciseRunner;
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Expected a string. Got: "stdClass"'
        );
        $runner->registerCheck($this->getMock(CheckInterface::class), new stdClass);
    }

    public function testRegisterCheck()
    {
        $runner = new ExerciseRunner;
        $runner->registerCheck($this->getMock(CheckInterface::class), 'SomeInterface');
    }

    public function testRunExerciseOnlyRunsRequiredChecks()
    {
        $runner = new ExerciseRunner;
        $doNotRunMe = $this->getMock(CheckInterface::class);
        $runner->registerCheck($doNotRunMe, StdOutExerciseCheck::class);

        $doNotRunMe
            ->expects($this->never())
            ->method('check');

        $result = $runner->runExercise($this->getMock(ExerciseInterface::class), 'some-file.php');
        $this->assertInstanceOf(ResultAggregator::class, $result);
        $this->assertTrue($result->isSuccessful());
    }

    public function testRunExerciseWithRequiredChecks()
    {
        $runner = new ExerciseRunner;
        $runMe = $this->getMock(CheckInterface::class);
        $runner->registerCheck($runMe, StdOutExerciseCheck::class);

        $runMe
            ->expects($this->once())
            ->method('check')
            ->will($this->returnValue(new Success));

        $result = $runner->runExercise($this->getMock(HelloWorld::class), 'some-file.php');
        $this->assertInstanceOf(ResultAggregator::class, $result);
        $this->assertTrue($result->isSuccessful());
    }

    public function testReturnEarly()
    {
        $runner = new ExerciseRunner;
        $runMe = $this->getMock(CheckInterface::class);
        $runMe
            ->expects($this->once())
            ->method('check')
            ->will($this->returnValue(new Failure('nope')));

        $runMe
            ->expects($this->once())
            ->method('breakChainOnFailure')
            ->will($this->returnValue(true));

        $doNotRunMe = $this->getMock(CheckInterface::class);
        $doNotRunMe
            ->expects($this->never())
            ->method('check');

        $runner->registerCheck($runMe, StdOutExerciseCheck::class);
        $runner->registerCheck($doNotRunMe, StdOutExerciseCheck::class);

        $result = $runner->runExercise($this->getMock(HelloWorld::class), 'some-file.php');
        $this->assertInstanceOf(ResultAggregator::class, $result);
        $this->assertFalse($result->isSuccessful());
    }
}

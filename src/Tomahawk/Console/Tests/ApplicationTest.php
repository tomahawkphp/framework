<?php

namespace Tomahawk\Console\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\HttpKernel\TestKernel;
use Symfony\Component\Console\Tester\ApplicationTester;
use Tomahawk\Console\Application;
use Tomahawk\Console\Tests\Commands\ACommand;
use Tomahawk\Console\Tests\Commands\BCommand;

class ApplicationTest extends TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/Commands/');
    }

    public function testRegister()
    {
        $app = new TestKernel('prod', false);
        $application = new Application($app);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);

        $application->add($foo = new ACommand());
        $commands = $application->all();
        $this->assertEquals($foo, $commands['foo:bar'], '->add() registers a command');

        $application->addCommands(array($foo = new ACommand(), $foo1 = new BCommand()));
        $commands = $application->all();
        $this->assertEquals(array($foo, $foo1), array($commands['foo:bar'], $commands['foo:bar1']), '->addCommands() registers an array of commands');

        $tester->run(array('command' => 'foo:bar'));

    }
}

<?php

namespace Tomahawk\Console\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Console\Application;
use Tomahawk\Console\Test\Commands\TestCallCommand;
use Tomahawk\Console\Test\Commands\TestCallSilentCommand;
use Tomahawk\Console\Test\Commands\TestCommand;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\KernelInterface;

/**
 * Class CommandTest
 * @package Tomahawk\Console\Tests
 */
class CommandTest extends TestCase
{
    public function testCall()
    {
        $application = new Application($this->getKernel(true));
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->addCommands([
            new TestCommand(),
            new TestCallCommand(),
        ]);
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo:testcall']);

        $this->assertEquals('interact called'.PHP_EOL .'execute called'.PHP_EOL . 'interact called'.PHP_EOL .'execute called'.PHP_EOL, $tester->getDisplay());
    }

    public function testCallSilent()
    {
        $application = new Application($this->getKernel(true));
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->addCommands([
            new TestCommand(),
            new TestCallSilentCommand(),
        ]);
        $tester = new ApplicationTester($application);

        $tester->run(['command' => 'foo:test-call-silent', '--quiet' => true]);

        $this->assertEquals('', $tester->getDisplay());
    }

    private function getKernel($useDispatcher = false)
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        if ($useDispatcher) {

            $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
            $dispatcher
                ->expects($this->atLeastOnce())
                ->method('dispatch');

            $container
                ->expects($this->atLeastOnce())
                ->method('get')
                ->with($this->equalTo('event_dispatcher'))
                ->will($this->returnValue($dispatcher));
        }

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue([]));

        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container));

        return $kernel;
    }
}

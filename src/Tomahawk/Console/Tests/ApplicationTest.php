<?php

namespace Tomahawk\Console\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Console\ContainerAwareCommand;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\HttpKernel\KernelInterface;
use PHPUnit\Framework\TestCase;
use Tomahawk\HttpKernel\TestKernel;
use Symfony\Component\Console\Tester\ApplicationTester;
use Tomahawk\Console\Application;
use Tomahawk\Console\Test\Commands\ACommand;
use Tomahawk\Console\Test\Commands\BCommand;

/**
 * Application.
 *
 * Based on the Symfony FrameworkBundle Console Application
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApplicationTest extends TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/../Test/Commands/');
    }

    public function testBundleInterfaceImplementation()
    {
        $bundle = $this->getMockBuilder(BundleInterface::class)->getMock();

        $kernel = $this->getKernel(array($bundle), true);

        $application = new Application($kernel);
        $application->doRun(new ArrayInput(array('list')), new NullOutput());
    }

    public function testBundleCommandsAreRegistered()
    {
        $bundle = $this->getMockBuilder(Bundle::class)->getMock();
        $bundle->expects($this->once())->method('registerCommands');

        $kernel = $this->getKernel(array($bundle), true);

        $application = new Application($kernel);
        $application->doRun(new ArrayInput(array('list')), new NullOutput());

        // Calling twice: registration should only be done once.
        $application->doRun(new ArrayInput(array('list')), new NullOutput());
    }

    public function testBundleCommandsAreRetrievable()
    {
        $bundle = $this->getMockBuilder(Bundle::class)->getMock();
        $bundle->expects($this->once())->method('registerCommands');

        $kernel = $this->getKernel(array($bundle));

        $application = new Application($kernel);
        $application->all();

        // Calling twice: registration should only be done once.
        $application->all();
    }

    public function testBundleSingleCommandIsRetrievable()
    {
        $bundle = $this->getMockBuilder(Bundle::class)->getMock();
        $bundle->expects($this->once())->method('registerCommands');

        $kernel = $this->getKernel(array($bundle));

        $application = new Application($kernel);

        $command = new Command('example');
        $application->add($command);

        $this->assertSame($command, $application->get('example'));
    }

    public function testBundleCommandCanBeFound()
    {
        $bundle = $this->getMockBuilder(Bundle::class)->getMock();
        $bundle->expects($this->once())->method('registerCommands');

        $kernel = $this->getKernel(array($bundle));

        $application = new Application($kernel);

        $command = new Command('example');
        $application->add($command);

        $this->assertSame($command, $application->find('example'));
    }

    public function testBundleCommandCanBeFoundByAlias()
    {
        $bundle = $this->getMockBuilder(Bundle::class)->getMock();
        $bundle->expects($this->once())->method('registerCommands');

        $kernel = $this->getKernel(array($bundle));

        $application = new Application($kernel);

        $command = new Command('example');
        $command->setAliases(array('alias'));
        $application->add($command);

        $this->assertSame($command, $application->find('alias'));
    }

    public function testBundleCommandsHaveRightContainer()
    {
        $command = $this->getMockForAbstractClass(ContainerAwareCommand::class, array('foo'), '', true, true, true, array('setContainer'));
        $command->setCode(function () {});
        $command->expects($this->exactly(2))->method('setContainer');

        $application = new Application($this->getKernel(array(), true));
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->add($command);
        $tester = new ApplicationTester($application);

        // set container is called here
        $tester->run(array('command' => 'foo'));

        // as the container might have change between two runs, setContainer must called again
        $tester->run(array('command' => 'foo'));
    }


    private function getKernel(array $bundles, $useDispatcher = false)
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
            ->will($this->returnValue($bundles));

        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container));

        return $kernel;
    }
}

<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Tomahawk\Cache\CacheInterface;
use Tomahawk\Console\Application;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit_Framework_TestCase as TestCase;

class CacheClearCommandTest extends TestCase
{
    public function testCommand()
    {
        $command = new CacheClearCommand();

        $container = new Container();

        $filesystem = $this->getFilesystemMock();

        $filesystem->expects($this->exactly(2))
            ->method('exists')
            ->will($this->returnValue(true));

        $filesystem->expects($this->once())
            ->method('rename');

        $filesystem->expects($this->exactly(2))
            ->method('remove');

        $filesystem->expects($this->once())
            ->method('mkdir');

        $container->set('filesystem', $filesystem);

        $commandTester = $this->getCommandTester($command, $container);

        $commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cache directory does not exist.
     */
    public function testCommandNoCacheDirectory()
    {
        $command = new CacheClearCommand();

        $container = new Container();

        $filesystem = $this->getFilesystemMock();

        $filesystem->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

        $container->set('filesystem', $filesystem);

        $commandTester = $this->getCommandTester($command, $container);

        $commandTester->execute(array('command' => $command->getName()));
    }


    /**
     * @param Command $command
     * @param ContainerInterface $container
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, ContainerInterface $container)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container->set('kernel', $application->getKernel());

        /*if ( ! $container) {
            $container = $application->getKernel()->getContainer();
        }

        $container->set('cache', $this->getCacheMock());
        $container->set('filesystem', $this->getFilesystemMock());*/


        $application->add($command);

        $command = $application->find('cache:clear');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function getCacheMock()
    {
        $cache = $this->getMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('flush');

        return $cache;
    }

    public function getFilesystemMock()
    {
        $filesystem = $this->getMock(Filesystem::class);

        return $filesystem;
    }
}

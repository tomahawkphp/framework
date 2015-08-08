<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use Tomahawk\Test\TestCase;

class CacheClearCommandTest extends TestCase
{
    public function testCommand()
    {
        $command = new CacheClearCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @return CommandTester
     */
    protected function getCommandTester(Command $command)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('cache', $this->getCacheMock());

        $application->add($command);

        $command = $application->find('cache:clear');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function getCacheMock()
    {
        $cache = $this->getMock('Tomahawk\Cache\CacheInterface');

        $cache->expects($this->once())
            ->method('flush');

        return $cache;
    }
}

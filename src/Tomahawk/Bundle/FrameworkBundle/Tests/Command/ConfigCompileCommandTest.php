<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Bundle\FrameworkBundle\Command\ConfigCompileCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit\Framework\TestCase;

class ConfigCompileCommandTest extends TestCase
{
    public function testCommand()
    {
        $command = new ConfigCompileCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/Config has been compiled for environment/', $commandTester->getDisplay());
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @return CommandTester
     */
    protected function getCommandTester(Command $command)
    {
        $app = new TestKernel('prod', false);
        $app->setRootDir(__DIR__ .'/../Resources');
        $app->setProjectDir(__DIR__ .'/../Resources');
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('config', $this->getConfigMock());

        $application->add($command);

        $command = $application->find('config:compile');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function getConfigMock()
    {
        $config = $this->getMockBuilder('Tomahawk\Config\ConfigInterface')->getMock();

        $config->expects($this->once())
            ->method('get')
            ->will($this->returnValue(array(
                'auth' => array(),
                'cache' => array(),
            )));

        return $config;
    }
}

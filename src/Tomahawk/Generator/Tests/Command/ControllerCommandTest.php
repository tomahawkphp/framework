<?php

namespace Tomahawk\Generator\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Generator\Command\GenerateControllerCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit\Framework\TestCase;

class ControllerCommandTest extends TestCase
{
    public function testCommandWithNoActions()
    {
        $command = new GenerateControllerCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array(
            'command'    => $command->getName(),
            'controller' => 'User',
        ));
    }

    public function testCommandWithAction()
    {
        $command = new GenerateControllerCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array(
            'command'    => $command->getName(),
            'controller' => 'User',
            '--actions'    => array('getUser'),
        ));
    }

    public function testCommandWithActionAndPlaceholders()
    {
        $command = new GenerateControllerCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array(
            'command'    => $command->getName(),
            'controller' => 'User',
            '--actions'    => array(
                'getUser:{foo}'
            ),
        ));
    }

    public function testCommandWithInvalidAction()
    {
        $command = new GenerateControllerCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array(
            'command'    => $command->getName(),
            'controller' => 'User',
            '--actions'  => array(''),
        ));
    }

    protected function getGenerator()
    {
        $generator = $this->getMockBuilder('Tomahawk\Generator\ControllerGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        return $generator;
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @param $generator
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, $generator)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('controller_generator', $generator);

        $application->add($command);

        $command = $application->find('generate:controller');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}

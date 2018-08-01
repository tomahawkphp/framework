<?php

namespace Tomahawk\Generator\Tests\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Generator\Command\GenerateModelCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit\Framework\TestCase;

class ModelCommandTest extends TestCase
{
    public function testCommand()
    {
        $command = new GenerateModelCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array('command' => $command->getName(), 'model' => 'User'));
    }

    protected function getGenerator()
    {
        $generator = $this->getMockBuilder('Tomahawk\Generator\ModelGenerator')
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

        $container->set('model_generator', $generator);

        $application->add($command);

        $command = $application->find('generate:model');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}

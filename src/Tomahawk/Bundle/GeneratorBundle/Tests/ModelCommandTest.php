<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Bundle\GeneratorBundle\Command\GenerateModelCommand;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use Tomahawk\Test\TestCase;

class ModelCommandTest extends TestCase
{

    public function testCommandThrowsExceptionWhenBundleDoesntExist()
    {
        $command = new GenerateModelCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $error = false;
        try
        {
            $commandTester->execute(array('command' => $command->getName(), 'bundle' => 'Foo', 'model' => 'FooBar'));
        }
        catch(\InvalidArgumentException $e)
        {
            $error = true;

            $this->assertRegExp('/Bundle "Foo" does not exist/', $e->getMessage());
        }

        $this->assertTrue($error, 'Exception should be thrown when bundle does not exist');
    }

    public function testCommand()
    {
        $command = new GenerateModelCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array('command' => $command->getName(), 'bundle' => 'FooBundle', 'model' => 'User'));
    }

    protected function getGenerator()
    {
        $generator = $this->getMockBuilder('Tomahawk\Bundle\GeneratorBundle\Generator\ModelGenerator')
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

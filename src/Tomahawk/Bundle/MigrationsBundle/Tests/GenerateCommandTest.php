<?php

namespace Tomahawk\Bundle\MirgrationsBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use Tomahawk\Test\TestCase;
use Symfony\Component\Finder\Finder;
use Tomahawk\Bundle\MigrationsBundle\Command\GenerateCommand;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends TestCase
{
    protected $migrationName;

    public function testCommandWhenBundleIsntSet()
    {
        $command = new GenerateCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $error = false;
        try
        {
            $commandTester->execute(array('command' => $command->getName(), 'bundle' => 'Foo', 'name' => 'FooBar'));
        }
        catch(\InvalidArgumentException $e)
        {
            $error = true;

            $this->assertRegExp('/Bundle "Foo" does not exist/', $e->getMessage());
        }

        $this->assertTrue($error);
    }

    public function testCommandHandlesIOException()
    {
        $generator = $this->getGenerator();

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->throwException(new \Symfony\Component\Filesystem\Exception\IOException('IO Error')));

        $command = new GenerateCommand();
        $commandTester = $this->getCommandTester($command, $generator);

        $commandTester->execute(array('command' => $command->getName(), 'bundle' => 'FooBundle', 'name' => 'FooBar'));

        $this->assertRegExp('/Error writing to/', $commandTester->getDisplay());
    }

    public function testCommandHandlesRuntimeException()
    {
        $generator = $this->getGenerator();

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->throwException(new \RuntimeException('Runtime error generating file')));

        $command = new GenerateCommand();
        $commandTester = $this->getCommandTester($command, $generator);

        $commandTester->execute(array('command' => $command->getName(), 'bundle' => 'FooBundle', 'name' => 'FooBar'));

        $this->assertRegExp('/Runtime error generating file/', $commandTester->getDisplay());
    }

    protected function getGenerator()
    {
        $generator = $this->getMockBuilder('Tomahawk\Bundle\MigrationsBundle\Migration\MigrationGenerator')
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

        $container->set('migration_generator', $generator);

        $application->add($command);

        $command = $application->find('migration:generate');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}
<?php

namespace Tomahawk\Bundle\MirgrationsBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\EloquentBundle\Command\InstallCommand;

class InstallCommandTest extends TestCase
{
    protected $migrationName;


    public function testInstallGivesCorrectMessageOnSuccess()
    {
        $repo = $this->getMigrationRepo();
        $repo->expects($this->any())
            ->method('createRepository')
            ->will($this->returnValue(true));

        $command = new InstallCommand();
        $commandTester = $this->getCommandTester($command, $repo);

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/Migration table created/', $commandTester->getDisplay());
    }

    public function testInstallGivesCorrectMessageOnFail()
    {
        $repo = $this->getMigrationRepo();
        $repo->expects($this->any())
            ->method('createRepository')
            ->will($this->returnValue(false));

        $command = new InstallCommand();
        $commandTester = $this->getCommandTester($command, $repo);

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/Failed to create migration table/', $commandTester->getDisplay());
    }

    protected function getMigrationRepo()
    {
        $repo = $this->getMockBuilder('Tomahawk\Bundle\EloquentBundle\Migrator\MigrationRepo')
            ->disableOriginalConstructor()
            ->getMock();

        return $repo;
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @param $repo
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, $repo)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('migration_repo', $repo);

        $application->add($command);

        $command = $application->find('migration:install');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}
<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\GeneratorBundle\Command\GenerateBundleCommand;
use Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use PHPUnit\Framework\TestCase;

class BundleCommandTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testRuntimeExceptionIsThrownWhenNoOptions()
    {
        $command = new GenerateBundleCommand();
        $commandTester = $this->getCommandTester($command, $this->getGenerator());

        $commandTester->execute(array('command' => $command->getName()));

    }

    public function testBundleGenerationWithBundleOption()
    {
        $fileSystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')->getMock();
        $fileSystem->expects($this->once())
            ->method('isAbsolutePath')
            ->will($this->returnValue(false));

        $generator = $this->getGenerator();
        $generator->expects($this->once())
            ->method('generate');

        $command = new GenerateBundleCommand();
        $commandTester = $this->getCommandTester($command, $generator, $fileSystem);

        $commandTester->execute(array(
            'command'     => $command->getName(),
            '--namespace'   => 'MyCompany\MyCompanyBundle',
            '--dir'         => '/src',
            '--bundle-name' => 'MyCompanyBundle'
        ));
    }

    public function testBundleGenerationWithoutBundleOption()
    {
        $fileSystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')->getMock();
        $fileSystem->expects($this->once())
            ->method('isAbsolutePath')
            ->will($this->returnValue(false));

        $generator = $this->getGenerator();
        $generator->expects($this->once())
            ->method('generate');

        $command = new GenerateBundleCommand();
        $commandTester = $this->getCommandTester($command, $generator, $fileSystem);

        $commandTester->execute(array(
            'command'     => $command->getName(),
            '--namespace'   => 'MyCompany\MyCompanyBundle',
            '--dir'         => '/src',
        ));
    }

    protected function getGenerator()
    {
        $generator = $this->getMockBuilder('Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        return $generator;
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @param $generator
     * @param null $fileSystem
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, $generator, $fileSystem = null)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);


        $container = $application->getKernel()->getContainer();

        $container->set('bundle_generator', $generator);
        $container->set('filesystem', $fileSystem);

        $application->add($command);

        $command = $application->find('generate:bundle');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}

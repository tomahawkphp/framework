<?php

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\DI\Container;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Kernel;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Router;

use Symfony\Component\Console\Tester\ApplicationTester;
use Tomahawk\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Bundles\GeneratorBundle\Command\GenerateBundleCommand;

class GenerateBundleTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateBundleCommandNoOptions()
    {
        $this->setExpectedException('RuntimeException', 'The "namespace" option must be provided.');

        $kernel = $this->getAppKernel();

        $command = new GenerateBundleCommand();
        $command->setContainer($kernel->getContainer());
        $command->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    public function testGenerateBundleCommandMissingDirOption()
    {
        $this->setExpectedException('RuntimeException', 'The "dir" option must be provided.');

        $kernel = $this->getAppKernel();

        $command = new GenerateBundleCommand();
        $command->setContainer($kernel->getContainer());
        $command->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('--namespace' => 'Acme/AcmeBundle'));
    }

    public function testGenerateBundleCommandMissingNameOption()
    {
        $this->setExpectedException('RuntimeException', 'The "bundle-name" option must be provided.');

        $kernel = $this->getAppKernel();

        $command = new GenerateBundleCommand();
        $command->setContainer($kernel->getContainer());
        $command->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('--namespace' => 'Acme/AcmeBundle', '--dir' => '../src'));
    }

    protected function getAppKernel()
    {
        $container = new Container();

        $kernel = new AppKernelGenStub('prod', false);

        $container['event_dispatcher'] = new EventDispatcher();
        $container['kernel'] = $kernel;
        $kernel->setContainer($container);
        $kernel->boot();

        return $kernel;
    }
}

class AppKernelGenStub extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Tomahawk\Bundles\GeneratorBundle\GeneratorBundle
        );
    }
}
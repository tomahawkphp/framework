<?php

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\Kernel;
use Tomahawk\HttpKernel\Test\Bundles\FooBundle\FooBundle;
use Tomahawk\HttpKernel\Test\Bundles\FooBundle\Command\FooCommand;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\DI\Container;
use Tomahawk\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\HttpKernel\HttpKernel;

class BundleTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \Tomahawk\DI\ContainerInterface
     */
    protected $container;

    /**
     * @var
     */
    protected $matcher;

    protected $controllerResolver;

    public function setup()
    {
        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $this->eventDispatcher = new EventDispatcher();
        $this->container = new Container();
    }

    public function testRegisterCommands()
    {
        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);

        $application = new Application($app);

        $cmd = new FooCommand();

        $bundle = new FooBundle();
        $bundle->setContainer($this->container);

        $this->assertEquals('FooBundle', $bundle->getName());

        // Check again as the first call sets the name on the bundle
        $this->assertEquals('FooBundle', $bundle->getName());

        $bundle->registerCommands($application);

    }

    public function testRegisterCommandsIngoreCommandAsAService()
    {
        $commandClass = 'Tomahawk\HttpKernel\Test\Bundles\FooBundle\Command\FooCommand';

        $this->container->set('event_dispatcher', $this->eventDispatcher);

        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);

        $cmd = new FooCommand();
        $cmd->setContainer($this->container);
        $alias = 'console.command.'.strtolower(str_replace('\\', '_', $commandClass));
        $this->container->set($alias, $cmd);

        $application = new Application($app);

        $bundle = new FooBundle();
        $bundle->setContainer($this->container);

        $this->assertEquals('FooBundle', $bundle->getName());

        // Check again as the first call sets the name on the bundle
        $this->assertEquals('FooBundle', $bundle->getName());

        $bundle->registerCommands($application);

        $this->assertNull($bundle->registerCommands($application));

        $bundle->boot();
        $bundle->shutdown();

        $this->assertNull($bundle->getContainer());
    }
}

class TestAppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        if ($this->getEnvironment() == 'dev')
        {
            $bundles[] = new \Tomahawk\HttpKernel\Test\Bundles\BarBundle\BarBundle();
            $bundles[] = new \Tomahawk\HttpKernel\Test\Bundles\FooBundle\FooBundle();
        }

        return $bundles;
    }

}
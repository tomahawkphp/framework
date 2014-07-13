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
use Tomahawk\Bundle\FrameworkBundle\Command\GenerateModelCommand;

class FrameworkBundleTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function testCreateModelCommand()
    {
        /*$kernel = $this->getAppKernel();

        $command = new GenerateModelCommand();
        $command->setContainer($kernel->getContainer());

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('model' => 'User'));*/

    }


    protected function getAppKernel()
    {
        $container = new Container();

        $kernel = new AppKernelStub('prod', false);

        $container['event_dispatcher'] = new EventDispatcher();
        $container['kernel'] = $kernel;
        $kernel->setContainer($container);
        $kernel->boot();

        return $kernel;
    }

    protected function getHttpKernel()
    {
        $request = Request::create('/', 'GET');
        $context = new RequestContext();
        $context->fromRequest($request);

        $container = new Container();

        $controllerResolver = new ControllerResolver($container);

        $routeCollection = new RouteCollection();

        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            return new Response('hello');
        });

        $matcher = new UrlMatcher($router->getRoutes(), $context);


        $kernel = new AppKernelStub('prod', false);

        $container['event_dispatcher'] = new EventDispatcher();
        $container['http_kernel'] = $httpKernel = new HttpKernel($container['event_dispatcher'], $matcher, $controllerResolver);

        $this->container = $container;
        return $httpKernel;
    }
}

class AppKernelStub extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Tomahawk\Bundles\FrameworkBundle\FrameworkBundle()
        );
    }
}

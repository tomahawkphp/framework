<?php

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\DI\Container;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Router;

class WebProfilerBundleTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function testBundle()
    {
        $httpKernel = $this->getHttpKernel();

        $request = new Request();
        $response = new Response();

        $event = new FilterResponseEvent($httpKernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);


        $webBundle = new WebProfilerBundle();
        $webBundle->setContainer($this->container);
        $webBundle->boot();

        $this->container['event_dispatcher']->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());

        $webBundle->shutdown();

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

        $container['event_dispatcher'] = new EventDispatcher();
        $container['http_kernel'] = $httpKernel = new HttpKernel($container['event_dispatcher'], $matcher, $controllerResolver);

        $this->container = $container;
        return $httpKernel;
    }
}

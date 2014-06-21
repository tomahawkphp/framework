<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\Core\Application;
use Tomahawk\DI\DIContainer;
use Tomahawk\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Routing\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AppKernelTest extends PHPUnit_Framework_TestCase
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
     * @var \Tomahawk\Core\Container
     */
    protected $container;

    public function setup()
    {
        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $this->eventDispatcher = new EventDispatcher();
        $this->container = new DIContainer();
    }

    public function testAppKernel()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $router->get('/', 'home', function() {
            return new Response('Test');
        });

        $matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $app = new Application($this->eventDispatcher, $matcher, $controllerResolver);

        $app->setRoutes($router->getRoutes());
        $app->setContext($this->context);

        $response = $app->handle($this->request);

        $this->assertEquals('Test', $response->getContent());

    }

    public function testAppKernelRouteParams()
    {
        $this->request = Request::create('/user/tomgrohl', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);


        $router->get('/user/{username}', 'user', function(Request $request) {
            return new Response($request->get('username'));
        });


        $matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $app = new Application($this->eventDispatcher, $matcher, $controllerResolver);

        $app->setRoutes($router->getRoutes());
        $app->setContext($this->context);

        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }

    public function testAppKernelRouteParamsEndSlash()
    {
        $this->request = Request::create('/user/tomgrohl/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);


        $router->get('/user/{username}', 'user', function(Request $request) {
            return new Response($request->get('username'));
        });


        $matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $app = new Application($this->eventDispatcher, $matcher, $controllerResolver);

        $app->setRoutes($router->getRoutes());
        $app->setContext($this->context);

        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }

    public function testAppKernelWithEvents()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            return 'Test';
        });

        $matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $app = new Application($this->eventDispatcher, $matcher, $controllerResolver);

        $this->eventDispatcher->addListener(KernelEvents::VIEW, function(GetResponseForControllerResultEvent $event) {
            if (is_string($event->getControllerResult()))
            {
                $event->setResponse(new Response($event->getControllerResult()));
            }
        });

        $this->eventDispatcher->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) {

            if ($event->getResponse()->getContent() === 'Test')
            {
                $resp = new Response('changed');
                $event->setResponse($resp);
            }
        });

        $app->setRoutes($router->getRoutes());
        $app->setContext($this->context);

        $response = $app->handle($this->request);

        $this->assertEquals('changed', $response->getContent());
    }

}
<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
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
use Tomahawk\Http\HttpKernel;

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
     * @var \Tomahawk\DI\DIContainerInterface
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
        $this->container = new DIContainer();
    }

    public function testAppKernel()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $router->get('/', 'home', function() {
            return new Response('Test');
        });

        $router->get('/user/{username}', 'user', function(Request $request) {
            return new Response($request->get('username'));
        });

        $this->controllerResolver = new ControllerResolver($this->container);
        $this->matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $this->matcher, $this->controllerResolver);

        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);
        $response = $app->handle($this->request);
        $this->assertEquals('Test', $response->getContent());

    }

    public function testAppKernelRouteParams()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/user/{username}', 'user', function(Request $request) {
            return new Response($request->get('username'));
        });

        $this->request = Request::create('/user/tomgrohl', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $this->controllerResolver = new ControllerResolver($this->container);
        $this->matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $this->matcher, $this->controllerResolver);

        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);
        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }


    public function testAppKernelRouteParamsEndSlash()
    {
        $this->request = Request::create('/user/tomgrohl/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $router->get('/user/{username}', 'user', function(Request $request) {
            return new Response($request->get('username'));
        });

        $this->controllerResolver = new ControllerResolver($this->container);

        $this->request = Request::create('/user/tomgrohl', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $this->controllerResolver = new ControllerResolver($this->container);
        $this->matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $this->matcher, $this->controllerResolver);

        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);
        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }

    public function testAppKernelWithEvents()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            return 'Test';
        });

        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $this->controllerResolver = new ControllerResolver($this->container);
        $this->matcher = new UrlMatcher($router->getRoutes(), $this->context);
        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $this->matcher, $this->controllerResolver);

        $app = new TestAppKernel('prod', false);
        $app->setContainer($this->container);

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

        $response = $app->handle($this->request);

        $this->assertEquals('changed', $response->getContent());
    }

}

class TestAppKernel extends \Tomahawk\Http\Kernel
{

}
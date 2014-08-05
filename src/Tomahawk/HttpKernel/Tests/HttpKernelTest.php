<?php

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\DI\Container;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Tomahawk\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class HttpKernelTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function testResourceNotFoundException()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('doesnt-exist', 'GET');

        $httpRequest->handle($request);

    }
    
    public function testThrownNotFoundHttpException()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('error', 'GET');

        $httpRequest->handle($request);
    }


    public function testException()
    {
        $this->setExpectedException('Exception');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/', 'GET');

        $httpRequest->handle($request);
    }

    public function testExceptionCatchFalse()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/doesnt-exist', 'GET');

        $httpRequest->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
    }


    public function testRequestEventHasResponse()
    {
        $httpRequest = $this->getHttpKernel();


        $eventDispatcher = $this->container['event_dispatcher'];

        $eventDispatcher->addListener(KernelEvents::REQUEST, function(GetResponseEvent $event) {

            $event->setResponse(new Response('foobar'));

        });

        $request = Request::create('/', 'GET');
        $response = $httpRequest->handle($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('foobar', $response->getContent());
    }

    /*
    public function testResponseNotReturnedViewEvent()
    {
        $httpRequest = $this->getHttpKernel();


        $eventDispatcher = $this->container['event_dispatcher'];

        $eventDispatcher->addListener(KernelEvents::VIEW, function(GetResponseForControllerResultEvent $event) {

            if (is_string($event->getControllerResult()))
            {
                $event->setResponse(new Response($event->getControllerResult()));
            }
        });

        $request = Request::create('/test', 'GET');
        $response = $httpRequest->handle($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('baz', $response->getContent());
    }

    public function testResponseNotReturnedResponseThrowsException()
    {
        $this->setExpectedException('LogicException');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/test', 'GET');
        $httpRequest->handle($request);

    }

    public function testResponseNullResponseThrowsException()
    {
        $this->setExpectedException('LogicException', 'The controller must return a response (null given). Did you forget to add a return statement somewhere in your controller?');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/null', 'GET');
        $httpRequest->handle($request);

    }

    public function testResponseTrueResponseThrowsException()
    {
        $this->setExpectedException('LogicException', 'The controller must return a response (true given).');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/true', 'GET');
        $httpRequest->handle($request);

    }

    public function testResponseFalseResponseThrowsException()
    {
        $this->setExpectedException('LogicException', 'The controller must return a response (false given).');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/false', 'GET');
        $httpRequest->handle($request);

    }

    public function testResponseArrayResponseThrowsException()
    {
        $this->setExpectedException('LogicException', 'The controller must return a response (Array(0 => 1) given).');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/array', 'GET');
        $httpRequest->handle($request);

    }

    public function testResponseObjectResponseThrowsException()
    {
        $this->setExpectedException('LogicException', 'The controller must return a response (Object(stdClass) given).');
        $httpRequest = $this->getHttpKernel();

        $request = Request::create('/object', 'GET');
        $httpRequest->handle($request);
    }

    public function testResponseHandleExceptionWithStatusCode()
    {
        $httpRequest = $this->getHttpKernel();

        $eventDispatcher = $this->container['event_dispatcher'];

        $eventDispatcher->addListener(KernelEvents::EXCEPTION, function(GetResponseForExceptionEvent $event) {

            $exception = $event->getException();

            if ($exception instanceof \LogicException)
            {
                $response = new Response('logic exception');
                $response->headers->set('X-Status-Code', 500);
                $event->setResponse($response);
            }
        });

        $request = Request::create('/test', 'GET');
        $response = $httpRequest->handle($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('logic exception', $response->getContent());
    }*/

    public function testResponseHandleExceptionWithoutStatusCode()
    {
        $httpRequest = $this->getHttpKernel();

        /**
         * @var EventDispatcher $eventDispatcher
         */
        $eventDispatcher = $this->container['event_dispatcher'];

        $eventDispatcher->addListener(KernelEvents::EXCEPTION, function(GetResponseForExceptionEvent $event) {

            $exception = $event->getException();

            if ($exception instanceof \LogicException)
            {
                $response = new Response('logic exception');
                $event->setResponse($response);
            }
        });

        $request = Request::create('/test', 'GET');
        $response = $httpRequest->handle($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('logic exception', $response->getContent());
    }

    protected function getHttpKernel()
    {
        $request = Request::create('/', 'GET');
        $context = new RequestContext();
        $context->fromRequest($request);

        $this->container = new Container();

        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();

        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            $r = 5/0;
        });

        $router->get('/test', 'test', function() {

            return 'baz';
        });

        $router->get('/null', 'null', function() {

        });

        $router->get('/true', 'true', function() {
            return true;
        });

        $router->get('/array', 'array', function() {
            return array(1);
        });

        $router->get('/false', 'false', function() {
            return false;
        });

        $router->get('/object', 'object', function() {
            return new \stdClass();
        });

        $router->get('/resource', 'resource', function() {
            return @mysql_connect('localhost', 'mysql_user', 'mysql_pass');
        });
        
        $router->get('error', 'error', function() {
            throw new NotFoundHttpException;   
        });

        $matcher = new UrlMatcher($router->getRoutes(), $context);

        $eventDispatcher = new EventDispatcher();

        $request_stack = new \Symfony\Component\HttpFoundation\RequestStack();

        $routeListener = new RouterListener($matcher, $context, null, $request_stack);

        $eventDispatcher->addSubscriber($routeListener);

        $this->container['event_dispatcher'] = $eventDispatcher;
        $this->container['http_kernel'] = $httpKernel = new HttpKernel($this->container['event_dispatcher'], $controllerResolver, $request_stack);

        return $httpKernel;
    }

    /**
     * @dataProvider getStatusCodes
     */
    public function testHandleWhenAnExceptionIsHandledWithASpecificStatusCode($responseStatusCode, $expectedStatusCode)
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(KernelEvents::EXCEPTION, function ($event) use ($responseStatusCode, $expectedStatusCode) {
            $event->setResponse(new Response('', $responseStatusCode, array('X-Status-Code' => $expectedStatusCode)));
        });

        $resolver = $this->getResolver(function () { throw new \RuntimeException(); });

        //$resolver = new ControllerResolver($this->container);

        $kernel = new HttpKernel($dispatcher, $resolver);
        $response = $kernel->handle(new Request());

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertFalse($response->headers->has('X-Status-Code'));
    }

    public function getStatusCodes()
    {
        return array(
            array(200, 404),
            array(404, 200),
            array(301, 200),
            array(500, 200),
        );
    }

    protected function getResolver($controller = null)
    {
        if (null === $controller) {
            $controller = function () { return new Response('Hello'); };
        }

        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $resolver->expects($this->any())
            ->method('getController')
            ->will($this->returnValue($controller));
        $resolver->expects($this->any())
            ->method('getArguments')
            ->will($this->returnValue(array()));

        return $resolver;
    }
}

class HttpKernelStub extends HttpKernel
{

}
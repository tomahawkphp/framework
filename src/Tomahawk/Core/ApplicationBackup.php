<?php

namespace Tomahawk\Core;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing;

class Application extends HttpKernel implements AppKernelInterface
{

    const VERSION = '0.1.0';

    protected $booted = false;

    /**
     * @var \Symfony\Component\Routing\Matcher\UrlMatcherInterface
     */
    protected $matcher;
    /**
     * @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface
     */
    protected $resolver;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * @var
     */
    protected $templating;

    /**
     * @var array
     */
    protected $environments = array();

    /**
     * @var
     */
    protected $environment;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @var \Tomahawk\DI\DIContainer
     */
    protected $container;

    protected $paths;

    /**
     * @var \Tomahawk\Routing\Router
     */
    protected $router;

    /**
     * @var
     */
    protected $routes;

    public static $instance;

    public function __construct(EventDispatcherInterface $dispatcher = null, UrlMatcherInterface $matcher = null, ControllerResolverInterface $resolver = null, RequestStack $requestStack = null)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack ?: new RequestStack();
    }

    /**
     * @param \Tomahawk\Routing\Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return \Tomahawk\Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface $matcher
     */
    public function setMatcher($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @return \Symfony\Component\Routing\Matcher\UrlMatcherInterface
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param mixed $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param \Tomahawk\DI\DIContainer $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return \Tomahawk\DI\DIContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return \Symfony\Component\Routing\RequestContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return $this
     */
    public function boot()
    {
        if (true === $this->booted) {
            return $this;
        }
        /**
         * Check if we have a matcher set
         */
        if (!$this->matcher) {
            $matcher = new Routing\Matcher\UrlMatcher($this->getRoutes(), $this->context);
            $this->setMatcher($matcher);
            $this->setContext($this->context);
            $this->setResolver($this->resolver);
        }

        $this->booted = true;

        return $this;
    }

    /**
     * @return Response
     */
    public function run()
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->handle(Request::createFromGlobals());
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->requestStack->push($request);

        try {

            return $this->handleRaw($request, $type);

        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $e) {

            // Check if we are showing detailed errors

            if (false === $catch) {
                $this->finishRequest($request, $type);

                throw $e;
            }

            return $this->handleException($e, $request, $type);
        }

        return $this->filterResponse($response, $request, $type);
    }

    private function handleRaw(Request $request, $type = self::MASTER_REQUEST)
    {
        // request
        $event = new GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        $path = $this->formatPath($request->getPathInfo());

        $request->attributes->add($this->matcher->match($path));

        $controller = $this->resolver->getController($request);

        $event = new FilterControllerEvent($this, $controller, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
        $controller = $event->getController();

        $arguments = $this->resolver->getArguments($request, $controller);

        $route = $request->get('_route');

        $response = call_user_func_array($controller, $arguments);

        $before_events = explode('|', $request->get('before'));

        //var_dump($request->attributes->get('_beforeFilters'));
        //exit;

        $before_event = null;

        $before_events = array();

        if ($before_events) {
            //$before_event = new \BeforeEvent($this, $request, $type, $response);
        }

        // Fire all Before events - if any are set
        foreach ($before_events as $event) {
            $this->dispatcher->dispatch($event, $before_event);

            /**
             * If event has a response
             */
            if ($before_event->getResponse() && $before_event->isPropagationStopped()) {
                $response = $before_event->getResponse();
                break;
            }
        }

        // view
        if (!$response instanceof Response) {
            $event = new GetResponseForControllerResultEvent($this, $request, $type, $response);
            $this->dispatcher->dispatch(KernelEvents::VIEW, $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {
                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }

        return $this->filterResponse($response, $request, $type);
    }

    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param \Exception $e       An \Exception instance
     * @param Request    $request A Request instance
     * @param integer    $type    The type of the request
     *
     * @return Response A Response instance
     *
     * @throws \Exception
     */
    private function handleException(\Exception $e, $request, $type)
    {
        $event = new GetResponseForExceptionEvent($this, $request, $type, $e);
        $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);

            throw $e;
        }

        $response = $event->getResponse();

        // the developer asked for a specific status code
        if ($response->headers->has('X-Status-Code')) {
            $response->setStatusCode($response->headers->get('X-Status-Code'));

            $response->headers->remove('X-Status-Code');
        } elseif (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->filterResponse($response, $request, $type);
        } catch (\Exception $e) {
            return $response;
        }
    }

    /**
     * Filters a response object.
     *
     * @param Response $response A Response instance
     * @param Request  $request  An error message in case the response is not a Response object
     * @param integer  $type     The type of the request (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     *
     * @return Response The filtered Response instance
     *
     * @throws \RuntimeException if the passed object is not a Response instance
     */
    private function filterResponse(Response $response, Request $request, $type)
    {
        $event = new FilterResponseEvent($this, $request, $type, $response);

        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->finishRequest($request, $type);


        /*$after_events = explode('|', $request->get('after'));

        $after_event = null;

        dd($after_events);

        if ($after_events) {
            $after_event = new \BeforeEvent($this, $request, $type, $response);
        }

        // Fire all $after_events events - if any are set
        foreach ($after_events as $event) {
            $this->dispatcher->dispatch($event, $after_event);
        }*/

        return $event->getResponse();
    }

    /**
     * Publishes the finish request event, then pop the request from the stack.
     *
     * Note that the order of the operations is important here, otherwise
     * operations such as {@link RequestStack::getParentRequest()} can lead to
     * weird results.
     *
     * @param Request $request
     * @param int     $type
     */
    private function finishRequest(Request $request, $type)
    {
        $this->dispatcher->dispatch(KernelEvents::FINISH_REQUEST, new FinishRequestEvent($this, $request, $type));
        $this->requestStack->pop();
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }

    /**
     * Format Request Path
     *
     * @param $path
     * @return string
     */
    public function formatPath( $path )
    {
        if ($path === '/')
        {
            return $path;
        }

        $path = '/' . trim($path, '/') . '/';

        return $path;
    }

    public function setPaths($paths)
    {
        $this->paths = $paths;
        return $this;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getPath($path)
    {
        return array_get($this->paths, $path);
    }

    public static function setInstance(Application $instance)
    {
        static::$instance = $instance;
    }

    /**
     * @return \Tomahawk\Core\Application
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @param mixed $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @return mixed
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function setRequestStack($requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }

    /**
     * @param array $environments
     */
    public function setEnvironments($environments)
    {
        $this->environments = $environments;
    }

    /**
     * @return array
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    public function detectEnvironment()
    {
        $request = Request::createFromGlobals();

        $base = $request->getHost();

        foreach ($this->environments as $environment => $hosts)
        {
            // To determine the current environment, we'll simply iterate through the
            // possible environments and look for a host that matches this host in
            // the request's context, then return back that environment's names.
            foreach ((array) $hosts as $host)
            {
                if (str_is($host, $base))
                {
                    return $environment;
                }
            }
        }

        return 'production';
    }

}
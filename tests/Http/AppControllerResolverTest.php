<?php

use Tomahawk\Http\ResponseBuilder;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Encryption\Crypt;
use Tomahawk\Database\DatabaseManager;
use Tomahawk\Session\SessionManager;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Assets\AssetManager;
use Tomahawk\Http\HttpKernel;
use Tomahawk\DI\DIContainer;
use Tomahawk\Routing\Router;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Tomahawk\Forms\FormsManager;
use Tomahawk\Http\Response\Cookies;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\View\ViewGenerator;

class AppControllerResolverTest extends PHPUnit_Framework_TestCase
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
     * @var \Tomahawk\DI\DIContainer
     */
    protected $container;

    public function setup()
    {
        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $htmlBuilder = new HtmlBuilder();

        $this->eventDispatcher = new EventDispatcher();
        $this->container = new DIContainer();

        $this->container['Tomahawk\View\ViewGeneratorInterface'] = new ViewGenerator(array(
            __DIR__.'/views/%name%.php'
        ));
        $this->container['Tomahawk\Http\ResponseBuilderInterface'] = new ResponseBuilder();
        $this->container['Tomahawk\DI\DIContainerInterface'] = $this->container;
        $this->container['Tomahawk\Encryption\CryptInterface'] = new Crypt(str_repeat('a', 32));
        $this->container['Tomahawk\Forms\FormsManagerInterface'] = new FormsManager();
        $this->container['Tomahawk\Http\Response\CookiesInterface'] = new Cookies($this->request, array());
        $this->container['Tomahawk\Assets\AssetManagerInterface'] = new AssetManager($htmlBuilder);
        $this->container['Symfony\Component\HttpFoundation\Request'] = $this->request;
        $this->container['Tomahawk\Session\SessionInterface'] = new SessionManager(array(
            'session_type' => 'array',
            'session_name' => 'tomahawk_session'
        ));

        $this->container['Tomahawk\Database\DatabaseManager'] = new DatabaseManager();

        $this->container['Tomahawk\Cache\CacheInterface'] = new CacheManager(array(
            'driver' => 'array'
        ));

        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();

        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', 'TestController::get_index');
        $router->get('/thing', 'thing', 'TestController::get_thing');

        $matcher = new UrlMatcher($router->getRoutes(), $this->context);

        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $matcher, $controllerResolver);
    }

    public function testAppKernel()
    {

        $app = new TestApp('prod', false);

        $app->setContainer($this->container);

        $response = $app->handle($this->request);

        $this->assertEquals('Test', $response->getContent());

        $this->context->fromRequest($this->request);
        $this->request = Request::create('/thing/', 'GET');
        //$app->setContext($this->context);

        $response = $app->handle($this->request);

        $this->assertEquals('Test2', $response->getContent());
    }

}

class TestApp extends \Tomahawk\Http\Kernel
{

}

class TestController extends Controller
{
    public function get_index()
    {
        return $this->response->content('Test');
    }

    public function get_thing()
    {
        return $this->response->content('Test2');
    }
}
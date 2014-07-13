<?php

use Psr\Log\LoggerInterface;
use Tomahawk\HttpCore\ResponseBuilder;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Encryption\Crypt;
use Tomahawk\Database\DatabaseManager;
use Tomahawk\Session\SessionManager;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Kernel;
use Tomahawk\DI\Container;
use Tomahawk\Routing\Router;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Tomahawk\Forms\FormsManager;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\View\ViewGenerator;

class ControllerResolverTest extends PHPUnit_Framework_TestCase
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
     * @var \Tomahawk\DI\Container
     */
    protected $container;

    public function setup()
    {
        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $htmlBuilder = new HtmlBuilder();

        $this->eventDispatcher = new EventDispatcher();
        $this->container = new Container();

        $this->container['Tomahawk\View\ViewGeneratorInterface'] = new ViewGenerator(array(
            __DIR__.'/views/%name%.php'
        ));
        $this->container['Tomahawk\HttpCore\ResponseBuilderInterface'] = new ResponseBuilder();
        $this->container['Tomahawk\DI\ContainerInterface'] = $this->container;
        $this->container['Tomahawk\Encryption\CryptInterface'] = new Crypt(str_repeat('a', 32));
        $this->container['Tomahawk\Forms\FormsManagerInterface'] = new FormsManager();
        $this->container['Tomahawk\HttpCore\Response\CookiesInterface'] = new Cookies($this->request, array());
        $this->container['Tomahawk\Asset\AssetManagerInterface'] = new AssetManager($htmlBuilder);
        $this->container['Symfony\Component\HttpFoundation\Request'] = $this->request;
        $this->container['Tomahawk\Session\SessionInterface'] = new SessionManager(array(
            'session_type' => 'array',
            'session_name' => 'tomahawk_session'
        ));

        $resolver = Mockery::mock('Illuminate\Database\ConnectionResolverInterface');
        $this->container['Tomahawk\Database\DatabaseManager'] = new DatabaseManager($resolver);

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

    public function testNoControllerWithLogger()
    {
        $logger = Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning');


        $controllerResolver = new ControllerResolver($this->container, $logger);

        $request = Request::create('/', 'GET');

        $this->assertFalse($controllerResolver->getController($request));
    }

    public function testNoControllerWithoutLogger()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');

        $this->assertFalse($controllerResolver->getController($request));
    }

    public function testControllerFunction()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller', function() {

        });

        $this->assertTrue(false !== $controllerResolver->getController($request));
    }

    public function testControllerFunctionString()
    {

        function controller() {

        }

        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller', 'controller');

        $this->assertTrue(false !== $controllerResolver->getController($request));
    }

    public function testControllerInvokeableClass()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller', new TestInvokeableClass());

        $this->assertTrue(false !== $controllerResolver->getController($request));
    }

    public function testControllerInvokeableClassString()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller', 'TestInvokeableClass');

        $this->assertTrue(false !== $controllerResolver->getController($request));
    }

    public function testInvalidController()
    {
        $controllerResolver = new ControllerResolver($this->container);
        $this->setExpectedException('\InvalidArgumentException', 'Class "foobarbaz" does not exist.');

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','foobarbaz::action');

        $controllerResolver->getController($request);
    }

    public function testInvalidController2()
    {
        $controllerResolver = new ControllerResolver($this->container);
        $this->setExpectedException('\InvalidArgumentException', 'Unable to find controller "foobarbaz".');

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','foobarbaz');

        $controllerResolver->getController($request);
    }

    public function testGetArguments()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $controller = array(new self(), 'testGetArguments');
        $this->assertEquals(array(), $resolver->getArguments($request, $controller), '->getArguments() returns an empty array if the method takes no arguments');

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $controller = array(new self(), 'controllerMethod1');
        $this->assertEquals(array('foo'), $resolver->getArguments($request, $controller), '->getArguments() returns an array of arguments for the controller method');

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $controller = array(new self(), 'controllerMethod2');
        $this->assertEquals(array('foo', null), $resolver->getArguments($request, $controller), '->getArguments() uses default values if present');

        $request->attributes->set('bar', 'bar');
        $this->assertEquals(array('foo', 'bar'), $resolver->getArguments($request, $controller), '->getArguments() overrides default values if provided in the request attributes');

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $controller = function ($foo) {};
        $this->assertEquals(array('foo'), $resolver->getArguments($request, $controller));

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $controller = function ($foo, $bar = 'bar') {};
        $this->assertEquals(array('foo', 'bar'), $resolver->getArguments($request, $controller));

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $controller = new self();
        $this->assertEquals(array('foo', null), $resolver->getArguments($request, $controller));
        $request->attributes->set('bar', 'bar');
        $this->assertEquals(array('foo', 'bar'), $resolver->getArguments($request, $controller));

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $request->attributes->set('foobar', 'foobar');
        $controller = 'some_controller_function';
        $this->assertEquals(array('foo', 'foobar'), $resolver->getArguments($request, $controller));

        $request = Request::create('/');
        $request->attributes->set('foo', 'foo');
        $request->attributes->set('foobar', 'foobar');
        $controller = array(new self(), 'controllerMethod3');

        if (version_compare(PHP_VERSION, '5.3.16', '==')) {
            $this->markTestSkipped('PHP 5.3.16 has a major bug in the Reflection sub-system');
        } else {
            try {
                $resolver->getArguments($request, $controller);
                $this->fail('->getArguments() throws a \RuntimeException exception if it cannot determine the argument value');
            } catch (\Exception $e) {
                $this->assertInstanceOf('\RuntimeException', $e, '->getArguments() throws a \RuntimeException exception if it cannot determine the argument value');
            }
        }

        $request = Request::create('/');
        $controller = array(new self(), 'controllerMethod5');
        $this->assertEquals(array($request), $resolver->getArguments($request, $controller), '->getArguments() injects the request');



    }

    public function testRequiredParameter()
    {
        $this->setExpectedException('\RuntimeException', 'Controller "TestController2::action()" requires that you provide a value for the "$foo" argument (because there is no default value or because there is a non optional argument after this one).');
        $request = Request::create('/');
        $resolver = $this->createControllerResolver();
        $controller = array(new TestController2(), 'action');
        $resolver->getArguments($request, $controller);
    }

    public function testRequiredParameter2()
    {
        $this->setExpectedException('\RuntimeException', 'Controller "TestInvokeableClass" requires that you provide a value for the "$x" argument (because there is no default value or because there is a non optional argument after this one).');
        $request = Request::create('/');
        $resolver = $this->createControllerResolver();
        $controller = new TestInvokeableClass();
        $resolver->getArguments($request, $controller);
    }

    public function testRequiredParameter3()
    {
        $this->setExpectedException('\RuntimeException', 'Controller "some_controller_function" requires that you provide a value for the "$foo" argument (because there is no default value or because there is a non optional argument after this one).');
        $request = Request::create('/');
        $resolver = $this->createControllerResolver();
        $controller = 'some_controller_function';
        $resolver->getArguments($request, $controller);
    }


    public function testGetControllerWithFunction()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', 'some_controller_function');
        $controller = $resolver->getController($request);
        $this->assertSame('some_controller_function', $controller);
    }

    public function testGetControllerWithLambda()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', $lambda = function () {});
        $controller = $resolver->getController($request);
        $this->assertSame($lambda, $controller);
    }

    public function testGetControllerWithObjectAndInvokeMethod()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', $this);
        $controller = $resolver->getController($request);
        $this->assertSame($this, $controller);
    }

    public function testGetControllerWithObjectAndMethod()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', array($this, 'controllerMethod1'));
        $controller = $resolver->getController($request);
        $this->assertSame(array($this, 'controllerMethod1'), $controller);
    }

    public function testGetControllerWithClassAndMethod()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', array('Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest', 'controllerMethod4'));
        $controller = $resolver->getController($request);
        $this->assertSame(array('Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest', 'controllerMethod4'), $controller);
    }

    public function testGetControllerWithObjectAndMethodAsString()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', 'Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest::controllerMethod1');
        $controller = $resolver->getController($request);
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest', $controller[0], '->getController() returns a PHP callable');
    }

    public function testGetControllerWithClassAndInvokeMethod()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', 'Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest');
        $controller = $resolver->getController($request);
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Tests\Controller\ControllerResolverTest', $controller);
    }

    public function testGetControllerOnObjectWithoutInvokeMethod()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', new \stdClass());
        $resolver->getController($request);
    }


    /**
     * @dataProvider      getUndefinedControllers
     * @expectedException \InvalidArgumentException
     */
    public function testGetControllerOnNonUndefinedFunction($controller)
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', $controller);
        $resolver->getController($request);
    }

    public function testRequireParameter()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', array(new TestController2(), 'action'));
        $resolver->getController($request);
    }

    public function getUndefinedControllers()
    {
        return array(
            array('foo'),
            array('foo::bar'),
            array('stdClass'),
            array('ControllerResolverTest::bar'),
        );
    }

    protected function createControllerResolver(LoggerInterface $logger = null)
    {
        return new ControllerResolver($this->container, $logger);
    }

    public function __invoke($foo, $bar = null)
    {
    }

    public function controllerMethod1($foo)
    {
    }

    protected function controllerMethod2($foo, $bar = null)
    {
    }

    protected function controllerMethod3($foo, $bar = null, $foobar)
    {
    }

    protected static function controllerMethod4()
    {
    }

    protected function controllerMethod5(Request $request)
    {
    }

}



class TestApp extends Kernel
{
    public function registerBundles()
    {
        return array();
    }
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


class TestController2
{
    function action($foo)
    {

    }
}



class TestInvokeableClass
{
    public function __invoke($x)
    {
        return $x;
    }
}

function some_controller_function($foo, $foobar)
{
}

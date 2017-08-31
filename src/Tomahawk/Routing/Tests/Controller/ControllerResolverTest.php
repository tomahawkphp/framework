<?php

namespace Tomahawk\Routing\Tests\Controller;

use Psr\Log\LoggerInterface;
use TestBundle\Controller\HomeController;
use TestBundle\Controller\InvokeController;
use PHPUnit\Framework\TestCase;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Routing\Router;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\ClassLoader\ClassLoader;

class ControllerResolverTest extends TestCase
{
    /**
     * @var ClassLoader
     */
    protected $loader;

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
     * @var \Tomahawk\DependencyInjection\Container
     */
    protected $container;

    public function setup()
    {
        require_once(__DIR__.'/../Fixtures/functions.php');

        $this->request = Request::create('/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $this->eventDispatcher = new EventDispatcher();
        $this->container = new Container();

        $this->container['Tomahawk\View\ViewGeneratorInterface'] = $this->getMockBuilder('Tomahawk\View\ViewGeneratorInterface')->getMock();
        $this->container['Tomahawk\HttpCore\ResponseBuilderInterface'] = $this->getMockBuilder('Tomahawk\HttpCore\ResponseBuilderInterface')->getMock();
        $this->container['Tomahawk\DependencyInjection\ContainerInterface'] = $this->container;
        $this->container['Tomahawk\Forms\FormsManagerInterface'] = $this->getMockBuilder('Tomahawk\Forms\FormsManagerInterface')->getMock();
        $this->container['Tomahawk\HttpCore\Response\CookiesInterface'] = $this->getMockBuilder('Tomahawk\HttpCore\Response\CookiesInterface')->getMock();
        $this->container['Tomahawk\Asset\AssetManagerInterface'] = $this->getMockBuilder('Tomahawk\Asset\AssetManagerInterface')->getMock();
        $this->container['Symfony\Component\HttpFoundation\Request'] = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->container['Tomahawk\Session\SessionInterface'] = $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock();
        $this->container['Tomahawk\Cache\CacheInterface'] = $this->getMockBuilder('Tomahawk\Cache\CacheInterface')->getMock();

        $controllerResolver = new ControllerResolver($this->container);

        $routeCollection = new RouteCollection();

        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', 'TestController::get_index');
        $router->get('/thing', 'thing', 'TestController::get_thing');

        $this->container['http_kernel'] = new HttpKernel($this->eventDispatcher, $controllerResolver);
        $this->container['my.controller'] = function() {
            return new HomeController();
        };

        $this->container['invokeable.controller'] = function() {
            return new InvokeController();
        };

        $this->loader = new ClassLoader();

        $this->loader->addPrefixes(array(
            'TestBundle'      => __DIR__.'/../Fixtures',
        ));

        $this->loader->register();
    }

    protected function tearDown()
    {
        spl_autoload_unregister(array($this->loader, 'loadClass'));
        $this->loader = null;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class "foobarbaz" does not exist.
     */
    public function testInvalidController()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','foobarbaz::action');

        $controllerResolver->getController($request);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to find controller "foobarbaz".
     */
    public function testInvalidController2()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','foobarbaz');

        $controllerResolver->getController($request);
    }

    public function testGetControllerWithClassAndMethod()
    {
        $resolver = $this->createControllerResolver();

        $request = Request::create('/');
        $request->attributes->set('_controller', 'TestBundle\Controller\HomeController::homeAction');
        $controller = $resolver->getController($request);
        $this->assertInstanceOf('TestBundle\Controller\HomeController', $controller[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateController()
    {
        $controllerResolver = new ControllerResolver($this->container);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','TestController::get_noexist');

        $controllerResolver->getController($request);
    }

    public function testCreateControllerShortNotation()
    {
        $parser = $this->getParserMock();

        $parser->expects($this->once())
            ->method('parse')
            ->willReturn('TestBundle\Controller\HomeController::homeAction');

        $controllerResolver = new ControllerResolver($this->container, null, $parser);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','TestBundle:Home:home');

        $controllerResolver->getController($request);
    }

    public function testCreateControllerServiceNotation()
    {
        $controllerResolver = new ControllerResolver($this->container, null);

        $controller = $this->container->get('my.controller');

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','my.controller:homeAction');

        $this->assertEquals(array($controller, 'homeAction'), $controllerResolver->getController($request));
    }

    public function testCreateControllerInvokeable()
    {
        $controllerResolver = new ControllerResolver($this->container, null);

        $controller = $this->container->get('invokeable.controller');

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller','invokeable.controller');

        $this->assertEquals($controller, $controllerResolver->getController($request));
    }

    public function testCreateControllerWithParameters()
    {
        $controllerResolver = new ControllerResolver($this->container, null);

        $request = Request::create('/', 'GET');
        $request->attributes->set('_controller', 'Tomahawk\Routing\Test\Controller::homeAction');

        list($controller, $action) = $controllerResolver->getController($request);

        $this->assertInstanceOf('Tomahawk\Routing\Test\Controller', $controller);
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

    protected function getParserMock()
    {
        $mock = $this->getMockBuilder('Tomahawk\Routing\Controller\ControllerNameParser')
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}

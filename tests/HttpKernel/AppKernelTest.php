<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\DI\Container;
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
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Kernel;
use Tomahawk\HttpKernel\Bundle\Bundle;

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

    public function testConstructor()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $this->assertEquals($env, $kernel->getEnvironment());
        $this->assertEquals($debug, $kernel->isDebug());
        $this->assertFalse($kernel->isBooted());
        $this->assertLessThanOrEqual(microtime(true), $kernel->getStartTime());
        $this->assertNull($kernel->getContainer());
    }

    public function testBootSetsTheContainerToTheBundles()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->once())
            ->method('setContainer');

        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'getBundles'));
        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
    }

    public function testAppKernelNoDebug()
    {
        $app = $this->getApplication();

        $response = $app->handle($this->request);
        $this->assertEquals('Test', $response->getContent());

        $clone = clone($app);

        $this->assertNull($clone->getContainer());

        $this->assertNull($app->boot());
        $app->terminate($this->request, $response);
        $app->shutdown();

        $this->assertNull($app->shutdown());
        $this->assertNull($app->terminate($this->request, $response));

    }

    public function testAppKernelWithDebug()
    {
        $app = $this->getApplication('dev', true);

        $response = $app->handle($this->request);
        $this->assertEquals('Testprofiler', $response->getContent());

        $clone = clone($app);
        $this->assertNotNull($clone->getStartTime());


    }

    public function testAppKernelRouteParams()
    {
        $this->request = Request::create('/user/tomgrohl', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $app = $this->getApplication();

        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }


    public function testAppKernelRouteParamsEndSlash()
    {
        $this->request = Request::create('/user/tomgrohl/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $app = $this->getApplication();


        $response = $app->handle($this->request);
        $this->assertEquals('tomgrohl', $response->getContent());
    }

    public function testAppKernelWithEvents()
    {
        $app = $this->getApplication('dev', true);

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

        $this->assertEquals('changedprofiler', $response->getContent());
        $this->assertCount(1, $app->getBundles());
        $this->assertEquals('yay!', $app->getContainer()->get('web_profiler'));

        $app->terminate($this->request, $response);
        $app->shutdown();
    }

    /**
     * @param string $env
     * @param bool $debug
     * @return TestAppKernel
     */
    protected function getApplication($env = 'prod', $debug = false)
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
        $this->container->set('event_dispatcher', $this->eventDispatcher);
        $this->container->set('router', $router);

        $app = new TestAppKernel($env, $debug);
        $app->setContainer($this->container);

        return $app;
    }

    public function testCharset()
    {
        $app = $this->getApplication();

        $this->assertEquals('UTF-8', $app->getCharset());

    }

    public function testDirectories()
    {
        $app = $this->getApplication();

        $this->assertEquals('/var/www/vhosts/tomahawk/tests/HttpKernel', $app->getRootDir());
        $this->assertEquals('/var/www/vhosts/tomahawk/tests/HttpKernel/cache/prod', $app->getCacheDir());
        $this->assertEquals('/var/www/vhosts/tomahawk/tests/HttpKernel/logs', $app->getLogDir());
    }

    public function testPaths()
    {
        $app = $this->getApplication();

        $paths = array(
            'root' => $app->getRootDir(),
            'web'  => $app->getRootDir() . '/web'
        );

        $app->setPaths($paths);

        $this->assertCount(2, $app->getPaths());
        $this->assertEquals($app->getRootDir() . '/web', $app->getPath('web'));
    }

    public function testSerializeUnserialize()
    {
        $app = $this->getApplication();

        $serialized = serialize($app);

        $app = unserialize($serialized);

        $this->assertInstanceOf('TestAppKernel', $app);
    }

    public function testBundleExistance()
    {
        $this->setExpectedException('InvalidArgumentException');

        $app = $this->getApplication();

        $app->getBundle('foo');

    }

    public function testBundle()
    {
        $app = $this->getApplication('dev', false);

        $app->boot();

        $bundle = $app->getBundle('WebProfilerBundle', false);
        $this->assertTrue(is_array($bundle));

        $bundle2 = $app->getBundle('WebProfilerBundle', true);
        $this->assertInstanceOf('Tomahawk\Bundles\WebProfilerBundle\WebProfilerBundle', $bundle2);
    }

    /*public function testInitializeBundles()
    {
        $parent = $this->getBundle(null, null, 'ParentABundle');
        $child = $this->getBundle(null, 'ParentABundle', 'ChildABundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(array('registerBundles'));
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($parent, $child)))
        ;
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent), $map['ParentABundle']);
    }*/

    /*public function testInitializeBundlesSupportInheritanceCascade()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentBBundle');
        $parent = $this->getBundle(null, 'GrandParentBBundle', 'ParentBBundle');
        $child = $this->getBundle(null, 'ParentBBundle', 'ChildBBundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(array('registerBundles'));
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($grandparent, $parent, $child)))
        ;
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent, $grandparent), $map['GrandParentBBundle']);
        $this->assertEquals(array($child, $parent), $map['ParentBBundle']);
        $this->assertEquals(array($child), $map['ChildBBundle']);
    }*/

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Bundle "ChildCBundle" extends bundle "FooBar", which is not registered.
     */
    public function testInitializeBundlesThrowsExceptionWhenAParentDoesNotExists()
    {
        $child = $this->getBundle(null, 'FooBar', 'ChildCBundle');
        $kernel = $this->getKernel(array(), array($child));
        $kernel->boot();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Trying to register two bundles with the same name "DuplicateName"
     */
    public function testInitializeBundleThrowsExceptionWhenRegisteringTwoBundlesWithTheSameName()
    {
        $fooBundle = $this->getBundle(null, null, 'FooBundle', 'DuplicateName');
        $barBundle = $this->getBundle(null, null, 'BarBundle', 'DuplicateName');

        $kernel = $this->getKernel(array(), array($fooBundle, $barBundle));
        $kernel->boot();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Bundle "CircularRefBundle" can not extend itself.
     */
    public function testInitializeBundleThrowsExceptionWhenABundleExtendsItself()
    {
        $circularRef = $this->getBundle(null, 'CircularRefBundle', 'CircularRefBundle');

        $kernel = $this->getKernel(array(), array($circularRef));
        $kernel->boot();
    }


    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Bundle "ParentCBundle" is directly extended by two bundles "ChildC2Bundle" and "ChildC1Bundle".
     */
    public function testInitializeBundlesThrowsExceptionWhenABundleIsDirectlyExtendedByTwoBundles()
    {
        $parent = $this->getBundle(null, null, 'ParentCBundle');
        $child1 = $this->getBundle(null, 'ParentCBundle', 'ChildC1Bundle');
        $child2 = $this->getBundle(null, 'ParentCBundle', 'ChildC2Bundle');

        $kernel = $this->getKernel(array(), array($parent, $child1, $child2));
        $kernel->boot();
    }

    /**
     * Returns a mock for the BundleInterface
     *
     * @return BundleInterface
     */
    protected function getBundle($dir = null, $parent = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Bundle\BundleInterface')
            ->setMethods(array('getPath', 'getParent', 'getName', 'setContainer'))
            ->disableOriginalConstructor()
        ;

        if ($className) {
            $bundle->setMockClassName($className);
        }

        $bundle = $bundle->getMockForAbstractClass();

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(null === $bundleName ? get_class($bundle) : $bundleName))
        ;

        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($dir))
        ;

        $bundle
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent))
        ;

        return $bundle;
    }

    /**
     * Returns a mock for the abstract kernel.
     *
     * @param array $methods Additional methods to mock (besides the abstract ones)
     * @param array $bundles Bundles to register
     *
     * @return Kernel
     */
    protected function getKernel(array $methods = array(), array $bundles = array())
    {
        $methods[] = 'registerBundles';

        $kernel = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass()
        ;
        $kernel->expects($this->any())
            ->method('registerBundles')
            ->will($this->returnValue($bundles))
        ;
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

    protected function getKernelForTest(array $methods = array())
    {
        $kernel = $this->getMockBuilder('\KernelForTest')
            ->setConstructorArgs(array('test', false))
            ->setMethods($methods)
            ->getMock();
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

}

class TestAppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        if ($this->getEnvironment() == 'dev')
        {
            $bundles[] = new \Tomahawk\Bundles\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

}


class TestAppKernel2 extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        if ($this->getEnvironment() == 'dev')
        {
            $bundles[] = new \Tomahawk\Bundles\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Tomahawk\Bundles\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

}

class KernelForTest extends Kernel
{
    public function getBundleMap()
    {
        return $this->bundleMap;
    }

    public function registerBundles()
    {
        return array();
    }

    public function isBooted()
    {
        return $this->booted;
    }
}
<?php

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tomahawk\DI\Container;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
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

use Tomahawk\HttpKernel\Test\Kernel as KernelForTest;
use Tomahawk\HttpKernel\Test\KernelForTestWithBundles;
use Tomahawk\HttpKernel\Test\KernelStub;

class KernelTest extends TestCase
{
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

    public function testGetKernelParameters()
    {
        $kernel = new KernelStub('prod', false);
        $kernel->boot();

        $_SERVER['TOMAHAWK_THING'] = 'foonbar';

        $parameters = $kernel->getParameters();

        $this->assertCount(9, $parameters);
    }

    public function testGetHttpKernel()
    {
        $container = new Container();
        $kernel = new KernelStub('prod', false);

        $httpKernelMock = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $container->set('http_kernel', $httpKernelMock);
        $kernel->setContainer($container);

        $this->assertInstanceOf('Tomahawk\HttpKernel\HttpKernel', $kernel->getHttpKernelInstance());
    }

    public function testClone()
    {
        $kernel = new KernelForTest('dev', true);
        $kernel->setContainer(new Container());

        $clone = clone $kernel;
        $this->assertNull($clone->getContainer());
    }

    public function testBootInitializesBundlesAndContainer()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'initializeMiddleware'));
        $kernel->expects($this->once())
            ->method('initializeBundles');
        $kernel->expects($this->once())
            ->method('initializeContainer');
        $kernel->expects($this->once())
            ->method('initializeMiddleware');

        $kernel->boot();
    }
    public function testBootSetsTheContainerToTheBundles()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->once())
            ->method('setContainer');

        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'initializeMiddleware', 'getBundles'));
        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
    }

    public function testBootSetsTheBootedFlagToTrue()
    {
        // use test kernel to access isBooted()
        $kernel = $this->getKernelForTest(array('initializeBundles', 'initializeContainer', 'initializeMiddleware'));
        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }

    public function testBootKernelSeveralTimesOnlyInitializesBundlesOnce()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'initializeMiddleware'));
        $kernel->expects($this->once())
            ->method('initializeBundles');

        $kernel->boot();
        $kernel->boot();
    }

    public function testShutdownCallsShutdownOnAllBundles()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->once())
            ->method('shutdown');

        $kernel = $this->getKernel(array('initializeMiddleware'), array($bundle));

        $kernel->boot();
        $kernel->shutdown();

        // run again, which should do nothing
        $kernel->shutdown();
    }

    public function testShutdownGivesNullContainerToAllBundles()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->at(3))
            ->method('setContainer')
            ->with(null);

        $kernel = $this->getKernel(array('initializeMiddleware', 'getBundles'));
        $kernel->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
        $kernel->shutdown();
    }

    public function testHandleCallsHandleOnHttpKernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request();

        $httpKernelMock = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();
        $httpKernelMock
            ->expects($this->once())
            ->method('handle')
            ->with($request, $type, $catch);

        $kernel = $this->getKernel(array('initializeMiddleware', 'getHttpKernel'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->handle($request, $type, $catch);
    }

    public function testHandleBootsTheKernel()
    {
        $type = HttpKernelInterface::MASTER_REQUEST;
        $catch = true;
        $request = new Request();

        $httpKernelMock = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel = $this->getKernel(array('getHttpKernel', 'boot'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->expects($this->once())
            ->method('boot');

        $kernel->handle($request, $type, $catch);
    }

    public function testIsClassInActiveBundleFalse()
    {
        //$kernel = $this->getKernelMockForIsClassInActiveBundleTest();

        //$this->assertFalse($kernel->isClassInActiveBundle('Not\In\Active\Bundle'));
    }

    public function te2stLocateResourceOnDirectories()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/FooBundle', null, null, 'FooBundle'))))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/',
            $kernel->locateResource('@FooBundle/Resources/', __DIR__.'/Fixtures/Resources')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle',
            $kernel->locateResource('@FooBundle/Resources', __DIR__.'/Fixtures/Resources')
        );

        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->exactly(2))
            ->method('getBundle')
            ->will($this->returnValue(array($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle', null, null, 'Bundle1Bundle'))))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources/',
            $kernel->locateResource('@Bundle1Bundle/Resources/')
        );
        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/Resources',
            $kernel->locateResource('@Bundle1Bundle/Resources')
        );
    }

    public function testInitializeBundles()
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
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBundleExistance()
    {

        $kernel = new KernelStub('dev', true);

        $kernel->getBundle('foo');

    }

    public function testBarBundle()
    {
        $kernel = new KernelForTestWithBundles('test', true);

        $kernel->boot();

        $bundle = $kernel->getBundle('BarBundle', false);
        $this->assertTrue(is_array($bundle));

        $bundle2 = $kernel->getBundle('BarBundle', true);
        $this->assertInstanceOf('Tomahawk\HttpKernel\Test\Bundles\BarBundle\BarBundle', $bundle2);
    }

    public function testInitializeBundlesSupportInheritanceCascade()
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
    }

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

    public function testInitializeBundlesSupportsArbitraryBundleRegistrationOrder()
    {
        $grandparent = $this->getBundle(null, null, 'GrandParentCBundle');
        $parent = $this->getBundle(null, 'GrandParentCBundle', 'ParentCBundle');
        $child = $this->getBundle(null, 'ParentCBundle', 'ChildCBundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(array('registerBundles'));
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue(array($parent, $grandparent, $child)))
        ;
        $kernel->boot();

        $map = $kernel->getBundleMap();
        $this->assertEquals(array($child, $parent, $grandparent), $map['GrandParentCBundle']);
        $this->assertEquals(array($child, $parent), $map['ParentCBundle']);
        $this->assertEquals(array($child), $map['ChildCBundle']);
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

    public function testTerminateReturnsSilentlyIfKernelIsNotBooted()
    {
        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->never())
            ->method('getHttpKernel');

        $kernel->terminate(Request::create('/'), new Response());
    }

    public function testTerminateDelegatesTerminationOnlyForTerminableInterface()
    {
        // does not implement TerminableInterface
        $httpKernelMock = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernelInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $httpKernelMock
            ->expects($this->never())
            ->method('terminate');

        $kernel = $this->getKernel(array('initializeMiddleware', 'getHttpKernel'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());

        // implements TerminableInterface
        $httpKernelMock = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->setMethods(array('terminate'))
            ->getMock();

        $httpKernelMock
            ->expects($this->once())
            ->method('terminate');

        $kernel = $this->getKernel(array('initializeMiddleware', 'getHttpKernel'));
        $kernel->expects($this->exactly(2))
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernelMock));

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());
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

    public function testPaths()
    {
        $kernel = new KernelForTest('test', true);

        $paths = array(
            'root' => $kernel->getRootDir(),
            'web'  => $kernel->getRootDir() . '/web'
        );

        $kernel->setPaths($paths);

        $this->assertCount(2, $kernel->getPaths());
        $this->assertEquals($kernel->getRootDir() . '/web', $kernel->getPath('web'));
    }

    public function testGetRootDir()
    {
        $kernel = new KernelForTest('test', true);

        $this->assertEquals(realpath(__DIR__. '/../Test'), realpath($kernel->getRootDir()));
    }

    public function testSerialize()
    {
        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $serialized = serialize($kernel);

        $kernel = unserialize($serialized);

        $this->assertInstanceOf('Tomahawk\HttpKernel\Test\Kernel', $kernel);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsNotValid()
    {
        $this->getKernel()->locateResource('Foo');
    }

    public function testEnvParameters()
    {
        $methods = array(
            'getKernelParameters'
        );

        $kernel = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass();

        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

    /**
     * Returns a mock for the BundleInterface
     *
     * @param null $dir
     * @param null $parent
     * @param null $className
     * @param null $bundleName
     * @return BundleInterface
     */
    protected function getBundle($dir = null, $parent = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Bundle\BundleInterface')
            ->setMethods(array('getPath', 'getParent', 'getName'))
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
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Test\Kernel')
            ->setConstructorArgs(array('test', false))
            ->setMethods($methods)
            ->getMock();
        $p = new \ReflectionProperty($kernel, 'rootDir');
        $p->setAccessible(true);
        $p->setValue($kernel, __DIR__.'/Fixtures');

        return $kernel;
    }

}




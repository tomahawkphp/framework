<?php

namespace Tomahawk\HttpKernel\Tests;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\HttpKernel\Test\KernelWithBundleEvents;
use Tomahawk\HttpKernel\Test\KernelWithRoutes;
use PHPUnit\Framework\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\HttpKernel\Test\Kernel;
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
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer'));
        $kernel->expects($this->once())
            ->method('initializeBundles');
        $kernel->expects($this->once())
            ->method('initializeContainer');

        $kernel->boot();
    }
    public function testBootSetsTheContainerToTheBundles()
    {
        $bundle = $this->getMockBuilder('Tomahawk\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->once())
            ->method('setContainer');

        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer', 'getBundles'));
        $kernel->expects($this->exactly(2))
            ->method('getBundles')
            ->will($this->returnValue(array($bundle)));

        $kernel->boot();
    }

    public function testBootSetsTheBootedFlagToTrue()
    {
        // use test kernel to access isBooted()
        $kernel = $this->getKernelForTest(array('initializeBundles', 'initializeContainer'));
        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }

    public function testBootKernelSeveralTimesOnlyInitializesBundlesOnce()
    {
        $kernel = $this->getKernel(array('initializeBundles', 'initializeContainer'));
        $kernel->expects($this->once())
            ->method('initializeBundles');

        $kernel->boot();
        $kernel->boot();
    }

    public function testShutdownCallsShutdownOnAllBundles()
    {
        $bundle = $this->getMockBuilder('Tomahawk\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->once())
            ->method('shutdown');

        $kernel = $this->getKernel(array(), array($bundle));

        $kernel->boot();
        $kernel->shutdown();

        // run again, which should do nothing
        $kernel->shutdown();
    }

    public function testShutdownGivesNullContainerToAllBundles()
    {
        $bundle = $this->getMockBuilder('Tomahawk\HttpKernel\Bundle\Bundle')->getMock();
        $bundle->expects($this->at(4))
            ->method('setContainer')
            ->with(null);

        $kernel = $this->getKernel(array('getBundles'));
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

        $kernel = $this->getKernel(array('getHttpKernel'));
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

    public function testInitializeBundles()
    {
        $bundle = $this->getBundle(null, 'ABundle');

        // use test kernel so we can access getBundleMap()
        $kernel = $this->getKernelForTest(array('registerBundles', 'registerEvents'));
        $kernel
            ->expects($this->once())
            ->method('registerBundles')
            ->will($this->returnValue([$bundle]))
        ;
        $kernel->boot();

        $map = $kernel->getBundles();
        $this->assertEquals(['ABundle' => $bundle], $map);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBundleExistance()
    {

        $kernel = new KernelStub('dev', true);

        $kernel->getBundle('foo');

    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Trying to register two bundles with the same name "DuplicateName"
     */
    public function testInitializeBundleThrowsExceptionWhenRegisteringTwoBundlesWithTheSameName()
    {
        $fooBundle = $this->getBundle(null, 'FooBundle', 'DuplicateName');
        $barBundle = $this->getBundle(null, 'BarBundle', 'DuplicateName');

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
        $httpKernel = new TestKernel();

        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->once())
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernel));

        $this->assertFalse($httpKernel->terminateCalled, 'terminate() is never called if the kernel class does not implement TerminableInterface');

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());

        // implements TerminableInterface
        $httpKernel = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->setMethods(array('terminate'))
            ->getMock();

        $httpKernel
            ->expects($this->once())
            ->method('terminate');

        $kernel = $this->getKernel(array('getHttpKernel'));
        $kernel->expects($this->exactly(2))
            ->method('getHttpKernel')
            ->will($this->returnValue($httpKernel));

        $kernel->boot();
        $kernel->terminate(Request::create('/'), new Response());
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

    /**
     * @expectedException \RuntimeException
     */
    public function testLocateResourceThrowsExceptionWhenNameIsUnsafe()
    {
        $this->getKernel()->locateResource('@FooBundle/../bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenBundleDoesNotExist()
    {
        $this->getKernel()->locateResource('@FooBundle/config/routing.xml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLocateResourceThrowsExceptionWhenResourceDoesNotExist()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $kernel->locateResource('@Bundle1Bundle/config/routing.xml');
    }

    public function testLocateResourceReturnsTheFirstThatMatches()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $this->assertEquals(__DIR__.'/Fixtures/Bundle1Bundle/foo.txt', $kernel->locateResource('@Bundle1Bundle/foo.txt'));
    }

    public function testLocateResourceIgnoresDirOnNonResource()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Bundle1Bundle/foo.txt',
            $kernel->locateResource('@Bundle1Bundle/foo.txt', __DIR__.'/Fixtures')
        );
    }

    public function testLocateResourceReturnsTheDirOneForResourcesAndBundleOnes()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/Bundle1Bundle', null, 'Bundle1Bundle')))
        ;

        $this->assertEquals(array(
                __DIR__.'/Fixtures/Resources/Bundle1Bundle/foo.txt',
                __DIR__.'/Fixtures/Bundle1Bundle/Resources/foo.txt'),
            $kernel->locateResource('@Bundle1Bundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources', false)
        );
    }

    public function testLocateResourceReturnsTheDirOneForResources()
    {
        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->will($this->returnValue($this->getBundle(__DIR__.'/Fixtures/FooBundle', null, 'FooBundle')))
        ;

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/FooBundle/foo.txt',
            $kernel->locateResource('@FooBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources')
        );
    }

    /*public function testLocateResourceOverrideBundleAndResourcesFolders()
    {
        $parent = $this->getBundle(__DIR__.'/Fixtures/BaseBundle', null, 'BaseBundle', 'BaseBundle');
        $child = $this->getBundle(__DIR__.'/Fixtures/ChildBundle', 'ParentBundle', 'ChildBundle', 'ChildBundle');

        $kernel = $this->getKernel(array('getBundle'));
        $kernel
            ->expects($this->exactly(4))
            ->method('getBundle')
            ->will($this->returnValue(array($child, $parent)))
        ;

        $this->assertEquals(array(
                __DIR__.'/Fixtures/Resources/ChildBundle/foo.txt',
                __DIR__.'/Fixtures/ChildBundle/Resources/foo.txt',
                __DIR__.'/Fixtures/BaseBundle/Resources/foo.txt',
            ),
            $kernel->locateResource('@BaseBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources', false)
        );

        $this->assertEquals(
            __DIR__.'/Fixtures/Resources/ChildBundle/foo.txt',
            $kernel->locateResource('@BaseBundle/Resources/foo.txt', __DIR__.'/Fixtures/Resources')
        );

        try {
            $kernel->locateResource('@BaseBundle/Resources/hide.txt', __DIR__.'/Fixtures/Resources', false);
            $this->fail('Hidden resources should raise an exception when returning an array of matching paths');
        } catch (\RuntimeException $e) {
        }

        try {
            $kernel->locateResource('@BaseBundle/Resources/hide.txt', __DIR__.'/Fixtures/Resources', true);
            $this->fail('Hidden resources should raise an exception when returning the first matching path');
        } catch (\RuntimeException $e) {
        }
    }*/

    public function testRoutesPaths()
    {
        $routePaths = array(
            'dir1/routes.php',
            'dir2/routes.php',
        );

        $kernel = new KernelStub('test', false);

        $kernel->setRoutePaths($routePaths);

        $this->assertEquals($routePaths, $kernel->getRoutePaths());
    }

    public function testLoadEvents()
    {
        $kernel = new KernelWithBundleEvents('test', false);
        //$kernel->setEventDispatcher($eventDispatcher);

        $kernel->boot();
    }

    public function testLoadRoutes()
    {
        $kernel = new KernelWithRoutes('test', false);
        $kernel->boot();
        $this->assertCount(1, $kernel->getRoutePaths());
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
     * @param null $className
     * @param null $bundleName
     * @return BundleInterface
     */
    protected function getBundle($dir = null, $className = null, $bundleName = null)
    {
        $bundle = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Bundle\BundleInterface')
            ->setMethods(['getPath', 'getName'])
            ->disableOriginalConstructor();

        if ($className) {
            $bundle->setMockClassName($className);
        }

        $bundle = $bundle->getMockForAbstractClass();

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(null === $bundleName ? get_class($bundle) : $bundleName));

        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($dir));

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
        $methods[] = 'registerEvents';

        $kernel = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass();

        $kernel->expects($this->any())
            ->method('registerBundles')
            ->will($this->returnValue($bundles));

        $kernel->expects($this->any())
            ->method('registerEvents');

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


class TestKernel implements HttpKernelInterface
{
    public $terminateCalled = false;
    public function terminate()
    {
        $this->terminateCalled = true;
    }
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
    }
}
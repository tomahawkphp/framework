<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\RoutingProvider;

class RoutingProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\RoutingProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();
        $routingProvider = new RoutingProvider();
        $routingProvider->register($container);

        $this->assertTrue($container->has('route_listener'));
        $this->assertTrue($container->has('route_locator'));
        $this->assertTrue($container->has('route_loader'));
        $this->assertTrue($container->has('route_collection'));
        $this->assertTrue($container->has('controller_resolver'));
        $this->assertTrue($container->has('request_context'));
        $this->assertTrue($container->has('url_matcher'));
        $this->assertTrue($container->has('route_logger'));

        $this->assertInstanceOf('Symfony\Component\HttpKernel\EventListener\RouterListener', $container->get('route_listener'));
        $this->assertInstanceOf('Tomahawk\HttpKernel\Config\FileLocator', $container->get('route_locator'));
        $this->assertInstanceOf('Symfony\Component\Routing\Loader\PhpFileLoader', $container->get('route_loader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $container->get('route_collection'));
        $this->assertInstanceOf('Tomahawk\Routing\Controller\ControllerResolver', $container->get('controller_resolver'));
        $this->assertInstanceOf('Symfony\Component\Routing\RequestContext', $container->get('request_context'));
        $this->assertInstanceOf('Symfony\Component\Routing\Matcher\UrlMatcher', $container->get('url_matcher'));
        $this->assertEquals(null, $container->get('route_logger'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('request_stack', $this->getMock('Symfony\Component\HttpFoundation\RequestStack'));
        $container->set('kernel', $this->getKernel());
        $container->set('config', $this->getConfig());

        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('request.base_url', '', ''),
                array('request.host', 'localhost', 'localhost'),
                array('request.scheme', 'http', 'http'),
                array('request.http_port', 80, 80),
                array('request.https_port', 443, 443),
            )));

        return $config;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(__DIR__ .'/'));

        $kernel->expects($this->any())
            ->method('getRoutePaths')
            ->will($this->returnValue(array(
                __DIR__ .'/Resources/bundleroutes/routes.php'
            )));

        return $kernel;
    }
}

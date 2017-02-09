<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\FrameworkServiceProvider as FrameworkProvider;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Test\TestCase;

class FrameworkServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\FrameworkServiceProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

        $frameworkProvider = new FrameworkProvider();
        $frameworkProvider->register($container);

        $container->get('request_stack')->push(new Request());

        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcherInterface', $container->get('event_dispatcher'));
        $this->assertInstanceOf('Tomahawk\Asset\AssetManagerInterface', $container->get('asset_manager'));
        $this->assertInstanceOf('Symfony\Component\Filesystem\Filesystem', $container->get('filesystem'));
        $this->assertInstanceOf('Tomahawk\Forms\FormsManagerInterface', $container->get('form_manager'));
        $this->assertInstanceOf('Tomahawk\Html\HtmlBuilderInterface', $container->get('html_builder'));
        $this->assertInstanceOf('Tomahawk\Hashing\HasherInterface', $container->get('hasher'));
        $this->assertInstanceOf('Tomahawk\HttpCore\ResponseBuilderInterface', $container->get('response_builder'));
        $this->assertInstanceOf('Tomahawk\HttpCore\Response\CookiesInterface', $container->get('cookies'));
        $this->assertInstanceOf('Tomahawk\Bundle\FrameworkBundle\EventListener\LocaleListener', $container->get('locale_listener'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RequestStack', $container->get('request_stack'));
        $this->assertInstanceOf('Tomahawk\Url\UrlGeneratorInterface', $container->get('url_generator'));
        $this->assertInstanceOf('Symfony\Component\HttpKernel\HttpKernelInterface', $container->get('http_kernel'));
        $this->assertSame($container, $container->get('Tomahawk\DependencyInjection\ContainerInterface'));

    }

    public function getContainer()
    {
        $container = new Container();
        $container->set('config', $this->getConfig());
        $container->set('kernel', $this->getKernel());
        $container->set('route_collection', new RouteCollection());
        $container->set('request_context', new RequestContext());
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));
        $container->set('controller_resolver', $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface'));
        $container->set('argument_resolver', $this->getMock('Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface'));
        
        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        return $config;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(__DIR__));

        $kernel->expects($this->any())
            ->method('getRoutePaths')
            ->will($this->returnValue(array(
                __DIR__ .'/Resources/bundleroutes/routes.php'
            )));

        return $kernel;
    }
}

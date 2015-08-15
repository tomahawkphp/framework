<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Bundle\FrameworkBundle\DI\FrameworkProvider;
use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;

class FrameworkProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\FrameworkProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

        $frameworkProvider = new FrameworkProvider();
        $frameworkProvider->register($container);


        $this->assertInstanceOf('Illuminate\Database\Capsule\Manager', $container->get('illuminate_database'));
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcherInterface', $container->get('event_dispatcher'));
        $this->assertInstanceOf('Tomahawk\Asset\AssetManagerInterface', $container->get('asset_manager'));
        $this->assertSame($container, $container->get('Tomahawk\DI\ContainerInterface'));
        $this->assertInstanceOf('Symfony\Component\Filesystem\Filesystem', $container->get('filesystem'));
        $this->assertInstanceOf('Tomahawk\Database\DatabaseManager', $container->get('database'));
        $this->assertInstanceOf('Tomahawk\Encryption\CryptInterface', $container->get('encrypter'));
        $this->assertInstanceOf('Tomahawk\Forms\FormsManagerInterface', $container->get('form_manager'));
        $this->assertInstanceOf('Tomahawk\Input\InputInterface', $container->get('input'));
        $this->assertInstanceOf('Tomahawk\Html\HtmlBuilderInterface', $container->get('html_builder'));
        $this->assertInstanceOf('Tomahawk\Hashing\HasherInterface', $container->get('hasher'));
        $this->assertInstanceOf('Tomahawk\HttpCore\ResponseBuilderInterface', $container->get('response_builder'));
        $this->assertInstanceOf('Tomahawk\HttpCore\Response\CookiesInterface', $container->get('cookies'));
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $container->get('logger'));
        $this->assertInstanceOf('Tomahawk\Bundle\FrameworkBundle\Events\LocaleListener', $container->get('locale_listener'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RequestStack', $container->get('request_stack'));
        $this->assertInstanceOf('Tomahawk\Url\UrlGeneratorInterface', $container->get('url_generator'));
        $this->assertInstanceOf('Symfony\Component\HttpKernel\HttpKernelInterface', $container->get('http_kernel'));

    }

    public function getContainer()
    {
        $container = new Container();
        $container->set('config', $this->getConfig());
        $container->set('kernel', $this->getKernel());
        $container->set('route_collection', new RouteCollection());
        $container->set('request_context', new RequestContext());
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));
        $container->set('request', new Request());
        $container->set('controller_resolver', $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface'));
        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('database.connections', null, array(
                    'default' => array(
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'database'  => 'test',
                        'username'  => 'root',
                        'password'  => '',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
                    ),
                    'laravel' => array(
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'database'  => 'laravel',
                        'username'  => 'root',
                        'password'  => '',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
                    )
                )),
                array('database.default', null, 'default'),
                array('database.fetch', null, \PDO::FETCH_CLASS),
                array('security.key', null, 'asdasdasdadsas'),
                array('monolog.path', null),
                array('monolog.name', null),
                array('translation.locale', null, 'en'),
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
            ->will($this->returnValue(__DIR__));

        $kernel->expects($this->any())
            ->method('getRoutePaths')
            ->will($this->returnValue(array(
                __DIR__ .'/Resources/bundleroutes/routes.php'
            )));

        return $kernel;
    }
}

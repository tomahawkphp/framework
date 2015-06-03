<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\DI\Container;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Routing\Router;

class WebProfilerBundleTest extends TestCase
{
    protected $container;

    public function testBundle()
    {
        $httpKernel = $this->getHttpKernel();

        $eventDispatcher = $this->container['event_dispatcher'];
        $event = new FilterResponseEvent($httpKernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $webBundle = new WebProfilerBundle();
        $webBundle->setContainer($this->container);
        $webBundle->boot();
        $webBundle->registerEvents($eventDispatcher);

        $eventDispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());

        $webBundle->shutdown();

    }

    protected function getHttpKernel()
    {
        $httpKernel = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $container = new Container();
        $container['event_dispatcher'] = new EventDispatcher();
        $container['http_kernel'] = $httpKernel;
        $container['config'] = $this->getConfigMock();

        $engine = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $container['templating'] = $engine;


        $connection = $this->getConnectionMock();

        $databaseManager = $this->getDatabaseManagerMock();

        $databaseManager->expects($this->once())
            ->method('connection')
            ->will($this->returnValue($connection));

        $connection->expects($this->once())
            ->method('getQueryLog')
            ->will($this->returnValue(array()));

        $database = $this->getDatabaseMock();

        $database->expects($this->exactly(2))
            ->method('getDatabaseManager')
            ->will($this->returnValue($databaseManager));



        $container['illuminate_database'] = $database;

        $this->container = $container;
        return $httpKernel;
    }

    protected function getDatabaseMock()
    {
        return $this->getMockBuilder('Illuminate\Database\Capsule\Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('getDatabaseManager'))
            ->getMock();
    }

    protected function getDatabaseManagerMock()
    {
        return $this->getMockBuilder('Illuminate\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->setMethods(array('connection'))
            ->getMock();
    }

    protected function getConnectionMock()
    {
        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        return $connection;
    }

    protected function getConfigMock()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');
        $config->expects($this->any())
            ->method('get')
            ->with('database.enabled')
            ->will($this->returnValue(true));

        return $config;
    }
}

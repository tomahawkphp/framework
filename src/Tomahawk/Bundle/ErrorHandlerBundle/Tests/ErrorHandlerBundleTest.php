<?php

namespace Tomahawk\Bundle\ErrorHandlerBundle\Tests;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\EventListener\DebugHandlersListener;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\ErrorHandlerBundle\ErrorHandlerBundle;
use Tomahawk\Bundle\ErrorHandlerBundle\Controller\ExceptionController;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Config\ConfigInterface;

class ErrorHandlerBundleTest extends TestCase
{
    public function testBundleBoot()
    {
        $container = $this->getMock(ContainerInterface::class);

        $container->expects($this->exactly(3))
            ->method('set')
            ->withConsecutive(
                [$this->equalTo('exception_listener')],
                [$this->equalTo('exception_controller')],
                [$this->equalTo('debug_handlers_listener')]
            );


        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();
    }

    public function testBundleRegisterEvents()
    {
        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);

        $eventDispatcher->expects($this->exactly(2))
            ->method('addSubscriber');

        $exceptionListener = $this->getMockBuilder(ExceptionListener::class)
            ->disableOriginalConstructor()
            ->getMock();

        $debugHandlersListener = $this->getMockBuilder(DebugHandlersListener::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMock(ContainerInterface::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['exception_listener', $exceptionListener],
                ['debug_handlers_listener', $debugHandlersListener],
            ]));

        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();
        $bundle->registerEvents($eventDispatcher);
    }

    public function testControllerIsBuiltCorrectly()
    {
        $kernel = $this->getKernel();

        $kernel->expects($this->once())
            ->method('isDebug')
            ->will($this->returnValue(true));

        $twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getContainer();
        $container->set('twig', $twig);
        $container->set('kernel', $kernel);

        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();

        $this->assertInstanceOf(ExceptionController::class, $container->get('exception_controller'));
    }

    public function testListenerIsBuiltCorrectly()
    {
        $logger = $this->getMock(LoggerInterface::class);

        $container = $this->getContainer();
        $container->set('logger', $logger);

        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();

        $this->assertInstanceOf(ExceptionListener::class, $container->get('exception_listener'));
    }

    public function testDebugListenerIsBuiltCorrectly()
    {
        $logger = $this->getMock(LoggerInterface::class);

        $container = $this->getContainer();
        $container->set('logger', $logger);

        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();

        $this->assertInstanceOf(DebugHandlersListener::class, $container->get('debug_handlers_listener'));
    }

    protected function getContainer()
    {
        $container = new Container();

        return $container;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $kernel;
    }
}

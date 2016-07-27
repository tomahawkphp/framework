<?php

namespace Tomahawk\Bundle\ErrorHandlerBundle\Tests;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

        $container->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                [$this->equalTo('exception_listener')],
                [$this->equalTo('exception_controller')]
            );


        $bundle = new ErrorHandlerBundle();
        $bundle->setContainer($container);
        $bundle->boot();
    }

    public function testBundleRegisterEvents()
    {
        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);

        $eventDispatcher->expects($this->once())
            ->method('addSubscriber');

        $exceptionListener = $this->getMockBuilder(ExceptionListener::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exceptionController = $this->getMockBuilder(ExceptionController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->will($this->returnValueMap([
                ['exception_listener', $exceptionListener],
                ['exception_controller', $exceptionController],
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

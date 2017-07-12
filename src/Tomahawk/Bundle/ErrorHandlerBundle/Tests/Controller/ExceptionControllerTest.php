<?php

namespace Tomahawk\Bundle\ErrorHandlerBundle\Tests\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\HttpCore\Request;
use Tomahawk\Bundle\ErrorHandlerBundle\Controller\ExceptionController;

class ExceptionControllerTest extends TestCase
{
    public function testControllerWithDebug()
    {
        $request = new Request();

        $exception = FlattenException::create(new Exception('error'), 500);

        $kernel = $this->getKernel(true);
        $twig = $this->getTwig();

        $twig->expects($this->never())
            ->method('render');

        $container = $this->getContainer($kernel, $twig);

        $exceptionController = new ExceptionController($container->get('twig'), $kernel->isDebug());
        $exceptionController->setContainer($container);

        $this->assertInstanceOf(Response::class, $exceptionController->showAction($request, $exception));
    }

    public function testControllerWithoutDebug()
    {
        $request = new Request();

        $loader = $this->getMockBuilder(\Twig_Loader_Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            $loader->expects($this->once())
                ->method('exists')
                ->will($this->returnValue(true));
        }
        else {

            $loader->expects($this->once())
                ->method('getSourceContext');
        }

        $exception = FlattenException::create(new Exception('error'), 404);

        $kernel = $this->getKernel(false);
        $twig = $this->getTwig($loader);

        $twig->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('ErrorHandlerBundle:Error:error404.twig'),
                ['exception' => $exception]
            )
            ->will($this->returnValue('404 file note found'));


        $container = $this->getContainer($kernel, $twig);

        $exceptionController = new ExceptionController($container->get('twig'), $kernel->isDebug());
        $exceptionController->setContainer($container);

        $this->assertInstanceOf(Response::class, $exceptionController->showAction($request, $exception));
    }

    public function testControllerWithoutDebugAndAltTwigLoader()
    {
        $request = new Request();

        $loader = $this->getMockBuilder(\Twig_Loader_Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $loader->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(false));

        $loader->expects($this->at(1))
            ->method('exists')
            ->will($this->returnValue(true));

        $exception = FlattenException::create(new Exception('error'), 404);

        $kernel = $this->getKernel(false);
        $twig = $this->getTwig($loader);

        $twig->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('ErrorHandlerBundle:Error:exception.twig'),
                ['exception' => $exception]
            )
            ->will($this->returnValue('404 file note found'));

        $container = $this->getContainer($kernel, $twig);

        $exceptionController = new ExceptionController($container->get('twig'), $kernel->isDebug());
        $exceptionController->setContainer($container);

        $this->assertInstanceOf(Response::class, $exceptionController->showAction($request, $exception));
    }

    public function testControllerWithoutDebugTemplateDoesntExist()
    {
        $request = new Request();

        $loader = $this->getMockBuilder(\Twig_Loader_Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();


        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            $loader->expects($this->exactly(2))
                ->method('exists')
                ->will($this->returnValue(false));
        }
        else {

            $loader->expects($this->exactly(2))
                ->method('getSourceContext')
                ->will($this->throwException(new \Twig_Error_Loader('error')));
        }

        $exception = FlattenException::create(new Exception('error'), 404);

        $kernel = $this->getKernel(false);
        $twig = $this->getTwig($loader);

        $twig->expects($this->never())
            ->method('render');

        $container = $this->getContainer($kernel, $twig);

        $exceptionController = new ExceptionController($container->get('twig'), $kernel->isDebug());
        $exceptionController->setContainer($container);

        $this->assertInstanceOf(Response::class, $exceptionController->showAction($request, $exception));
    }

    protected function getContainer($kernel, $twig)
    {
        $container = new Container();

        $container->set('twig', $twig);

        $container->set('kernel', $kernel);

        return $container;
    }

    protected function getTwig($loader = null)
    {
        if ( ! $loader) {
            $loader = $this->getMockBuilder(\Twig_LoaderInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        }

        $twig = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twig->expects($this->any())
            ->method('getCharset')
            ->will($this->returnValue('UTF-8'));

        $twig->expects($this->any())
            ->method('getLoader')
            ->will($this->returnValue($loader));

        return $twig;
    }

    protected function getKernel($debug = true)
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('isDebug')
            ->will($this->returnValue($debug));

        return $kernel;
    }
}

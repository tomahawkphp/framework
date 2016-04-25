<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\HttpKernel\Event\ExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListenerTest extends TestCase
{
    public function test404Exception()
    {
        $notFoundException = new NotFoundHttpException();

        $getResponseForExceptionEvent = new GetResponseForExceptionEvent(
            $this->getHttpKernel(),
            Request::createFromGlobals(),
            HttpKernelInterface::MASTER_REQUEST,
            $notFoundException
        );

        $templating = $this->getTemplating();

        $templating->expects($this->any())
            ->method('render')
            ->will($this->returnValue('404'));

        $logger = $this->getLogger();

        $config = $this->getConfig();

        $exceptionListener = new ExceptionListener(
            $templating,
            'prod',
            $config,
            $logger
        );

        $exceptionListener->onException($getResponseForExceptionEvent);

        $this->assertEquals('404', $getResponseForExceptionEvent->getResponse()->getContent());
    }

    public function test404ExceptionSetsDefaultResponseWhenNoTemplate()
    {
        $notFoundException = new NotFoundHttpException();

        $getResponseForExceptionEvent = new GetResponseForExceptionEvent(
            $this->getHttpKernel(),
            Request::createFromGlobals(),
            HttpKernelInterface::MASTER_REQUEST,
            $notFoundException
        );

        $templating = $this->getTemplating();

        $logger = $this->getLogger();

        $config = $this->getConfig(null, null);

        $exceptionListener = new ExceptionListener(
            $templating,
            'prod',
            $config,
            $logger
        );

        $exceptionListener->onException($getResponseForExceptionEvent);

        $this->assertEquals('404 - File Not Found', $getResponseForExceptionEvent->getResponse()->getContent());
    }

    public function testHttpException()
    {
        $httpException = new HttpException('403');

        $getResponseForExceptionEvent = new GetResponseForExceptionEvent(
            $this->getHttpKernel(),
            Request::createFromGlobals(),
            HttpKernelInterface::MASTER_REQUEST,
            $httpException
        );

        $templating = $this->getTemplating();

        $templating->expects($this->any())
            ->method('render')
            ->will($this->returnValue('500'));

        $logger = $this->getLogger();

        $config = $this->getConfig();

        $exceptionListener = new ExceptionListener(
            $templating,
            'prod',
            $config,
            $logger
        );

        $exceptionListener->onException($getResponseForExceptionEvent);

        $this->assertEquals('500', $getResponseForExceptionEvent->getResponse()->getContent());
    }

    public function testHttpExceptionSetsDefaultResponseWhenNoTemplate()
    {
        $httpException = new HttpException('403');

        $getResponseForExceptionEvent = new GetResponseForExceptionEvent(
            $this->getHttpKernel(),
            Request::createFromGlobals(),
            HttpKernelInterface::MASTER_REQUEST,
            $httpException
        );

        $templating = $this->getTemplating();

        $logger = $this->getLogger();

        $config = $this->getConfig(null, null);

        $exceptionListener = new ExceptionListener(
            $templating,
            'prod',
            $config,
            $logger
        );

        $exceptionListener->onException($getResponseForExceptionEvent);

        $this->assertEquals('500 - Internal Server Error', $getResponseForExceptionEvent->getResponse()->getContent());
    }

    public function testException()
    {
        $httpException = new \Exception();

        $getResponseForExceptionEvent = new GetResponseForExceptionEvent(
            $this->getHttpKernel(),
            Request::createFromGlobals(),
            HttpKernelInterface::MASTER_REQUEST,
            $httpException
        );

        $templating = $this->getTemplating();

        $templating->expects($this->any())
            ->method('render')
            ->will($this->returnValue('500'));

        $logger = $this->getLogger();

        $config = $this->getConfig();

        $exceptionListener = new ExceptionListener(
            $templating,
            'prod',
            $config,
            $logger
        );

        $exceptionListener->onException($getResponseForExceptionEvent);

        $this->assertEquals('500', $getResponseForExceptionEvent->getResponse()->getContent());
    }

    protected function getTemplating()
    {
        $templating = $this->getMock('Symfony\Component\Templating\EngineInterface');

        return $templating;
    }

    protected function getLogger()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        return $logger;
    }

    protected function getConfig($path404 = 'path/to/file.php', $path500 = 'path/to/file.php')
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('error.template_404', null, $path404),
                array('error.template_50x', null, $path500),
            )));

        return $config;
    }

    protected function getHttpKernel()
    {
        $httpKernel = $this->getMockBuilder('Tomahawk\HttpKernel\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();

        return $httpKernel;
    }
}

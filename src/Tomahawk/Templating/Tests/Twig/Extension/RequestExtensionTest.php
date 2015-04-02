<?php

namespace Tomahawk\Templating\Tests\Twig\Extension;

use Tomahawk\Test\TestCase;
use Tomahawk\Templating\Twig\Extension\RequestExtension;

class RequestExtensionTest extends TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testExceptionIsThrown()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock());

        $requestExtension->getRequest();
    }

    public function testCorrectNumberOfFunctionsAreReturned()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock($this->getRequestMock()));

        $this->assertCount(3, $requestExtension->getFunctions());
    }

    public function testExtensionNameIsReturned()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock($this->getRequestMock()));

        $this->assertEquals('request', $requestExtension->getName());
    }

    public function testRequestFunction()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock($this->getRequestMock()));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $requestExtension->getRequest());
    }

    public function testLocaleFunction()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock($this->getRequestMock()));

        $this->assertEquals('en_GB', $requestExtension->getLocale());
    }

    public function testParameterFunction()
    {
        $requestExtension = new RequestExtension($this->getRequestStackMock($this->getRequestMock()));

        $this->assertEquals('Tom', $requestExtension->getParameter('hello'));
    }

    protected function getRequestMock()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue('en_GB'));

        $request->expects($this->any())
            ->method('get')
            ->will($this->returnValue('Tom'));

        return $request;
    }

    protected function getRequestStackMock($request = null)
    {
        $stack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->getMock();

        $stack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        return $stack;
    }
}

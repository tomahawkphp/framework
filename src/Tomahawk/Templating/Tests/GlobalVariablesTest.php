<?php

use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\HttpCore\Request;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Templating\GlobalVariables;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Authentication\AuthenticationProviderInterface;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GlobalVariablesTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $container = $this->getMock(ContainerInterface::class);

        $auth = $this->getMock(AuthenticationProviderInterface::class);

        $session = $this->getMock(SessionInterface::class);

        $request = $this->getMock(Request::class);

        $kernel = $this->getMock(KernelInterface::class);

        $kernel->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('prod'));

        $kernel->expects($this->any())
            ->method('isDebug')
            ->will($this->returnValue(true));


        $request->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));

        $requestStack = $this->getMock(RequestStack::class);

        $requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $auth->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->getMock(UserInterface::class)));

        $container->expects($this->any())
            ->method('has')
            ->willReturnMap([
                ['request_stack', true],
            ]);

        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['auth', $auth],
                ['request_stack', $requestStack],
                ['kernel', $kernel],
            ]);

        $this->container = $container;
    }

    public function testGettingUser()
    {
        $globalVariables = new GlobalVariables($this->container);

        $this->assertInstanceOf(UserInterface::class, $globalVariables->getUser());
    }

    public function testGettingRequest()
    {
        $globalVariables = new GlobalVariables($this->container);

        $this->assertInstanceOf(Request::class, $globalVariables->getRequest());
    }

    public function testGettingSession()
    {
        $globalVariables = new GlobalVariables($this->container);

        $this->assertInstanceOf(SessionInterface::class, $globalVariables->getSession());
    }

    public function testGettingRequestAndSessionNotSet()
    {
        $container = $this->getContainer();

        $container->expects($this->any())
            ->method('has')
            ->willReturnMap([
                ['request_stack', false],
            ]);

        $globalVariables = new GlobalVariables($container);

        $this->assertNull($globalVariables->getRequest());
        $this->assertNull($globalVariables->getSession());
    }

    public function testEnvironment()
    {
        $globalVariables = new GlobalVariables($this->container);

        $this->assertEquals('prod', $globalVariables->getEnvironment());
    }

    public function testIsDebug()
    {
        $globalVariables = new GlobalVariables($this->container);

        $this->assertTrue($globalVariables->isDebug());
    }

    protected function getContainer()
    {
        $container = $this->getMock(ContainerInterface::class);

        return $container;
    }
}

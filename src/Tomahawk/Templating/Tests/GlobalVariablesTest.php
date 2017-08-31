<?php

use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\HttpCore\Request;
use PHPUnit\Framework\TestCase;
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
        $container = $this->createMock(ContainerInterface::class);

        $auth = $this->createMock(AuthenticationProviderInterface::class);

        $session = $this->createMock(SessionInterface::class);

        $request = $this->createMock(Request::class);

        $kernel = $this->createMock(KernelInterface::class);

        $kernel->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('prod'));

        $kernel->expects($this->any())
            ->method('isDebug')
            ->will($this->returnValue(true));


        $request->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));

        $requestStack = $this->createMock(RequestStack::class);

        $requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $auth->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->createMock(UserInterface::class)));

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
        $container = $this->createMock(ContainerInterface::class);

        return $container;
    }
}

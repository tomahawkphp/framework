<?php

namespace Tomahawk\Security\Csrf\Test;

use Tomahawk\Security\Csrf\CsrfServiceProvider;
use Tomahawk\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tomahawk\Security\Csrf\Token\TokenManagerInterface;

class CsrfServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Security\Csrf\CsrfServiceProvider
     */
    public function testProvider()
    {
        $eventDispatcher = $this->getEventDispatcher();

        $eventDispatcher->expects($this->once())
            ->method('addSubscriber');

        $container = $this->getContainer();

        $container->set('config', $this->getConfig());
        $container->set('session', $this->getSession());

        $provider = new CsrfServiceProvider();
        $provider->register($container);
        $provider->subscribe($container, $eventDispatcher);

        $this->assertInstanceOf(TokenManagerInterface::class, $container->get('security.csrf.tokenmanager'));
    }

    protected function getContainer()
    {
        return new Container();
    }

    protected function getSession()
    {
        return $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock();
    }

    protected function getConfig()
    {
        return $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock();
    }

    protected function getEventDispatcher()
    {
        return $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
    }
}

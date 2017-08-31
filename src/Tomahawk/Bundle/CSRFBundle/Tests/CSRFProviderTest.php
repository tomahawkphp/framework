<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Tomahawk\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\CSRFBundle\DependencyInjection\CSRFProvider;

class CSRFProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\CSRFBundle\DependencyInjection\CSRFProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

        $container->set('config', $this->getConfig());
        $container->set('session', $this->getSession());

        $csrfProvider = new CSRFProvider();
        $csrfProvider->register($container);

        $this->assertInstanceOf('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface', $container->get('security.csrf.tokenmanager'));
    }

    /**
     * @return Container
     */
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
}

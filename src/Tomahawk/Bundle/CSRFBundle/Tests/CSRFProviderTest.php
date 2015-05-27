<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider;

class CSRFProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

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
        return $this->getMock('Tomahawk\Session\SessionInterface');
    }
}

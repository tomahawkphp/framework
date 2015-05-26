<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
class CSRFProviderTest extends TestCase
{
    public function testProvider()
    {
        $eventDispatcher = $this->getEventDispatcher();

        $eventDispatcher->expects($this->once())
            ->method('addSubscriber');

        $container = $this->getContainer();

        $container->expects($this->once())
            ->method('set')
            ->with('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface');

        $container->expects($this->once())
            ->method('addAlias')
            ->with('security.csrf.tokenmanager', 'Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface');


        $container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap(array(
                array('event_dispatcher', $eventDispatcher),
                array('security.csrf.tokenmanager', $this->getMock('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface')),
            )));

        $csrfProvider = new CSRFProvider();
        $csrfProvider->register($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainer()
    {
        return $this->getMock('Tomahawk\DI\ContainerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcher()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}

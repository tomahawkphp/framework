<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\CSRFBundle\CSRFBundle;

class CSRFBundleTest extends TestCase
{
    public function testBundle()
    {
        $eventDispatcher = $this->getEventDispatcher();

        $eventDispatcher->expects($this->once())
            ->method('addSubscriber');

        $container = $this->getContainer();

        $bundle = new CSRFBundle();
        $bundle->setContainer($container);
        $bundle->boot();
        $bundle->registerEvents($eventDispatcher);
    }

    protected function getContainer()
    {
        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();

        $container->expects($this->once())
            ->method('register');

        $container->expects($this->once())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('security.csrf.tokenmanager', $this->getMockBuilder('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface')->getMock()),
            )));

        return $container;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcher()
    {
        return $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
    }
}

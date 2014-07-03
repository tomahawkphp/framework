<?php

namespace Tomahawk\Bundles\WebProfilerBundle;

use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WebProfilerBundle extends Bundle implements ContainerAwareInterface
{

    public function boot()
    {
        //$this->getEventDispatcher()->
    }

    public function shutdown()
    {

    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }
}
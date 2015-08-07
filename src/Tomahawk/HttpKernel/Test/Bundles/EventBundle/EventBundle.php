<?php

namespace Tomahawk\HttpKernel\Test\Bundles\EventBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBundle extends Bundle
{
    public function boot()
    {
        $this->container->set('event_dispatcher', new EventDispatcher());
    }

    /**
     * Register any events for the bundle
     *
     * This is called after all bundles have been boot so you get access
     * to all the services
     *
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener('event.name', array('class', 'method'));
    }

}

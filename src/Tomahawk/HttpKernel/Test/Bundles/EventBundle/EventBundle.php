<?php

namespace Tomahawk\HttpKernel\Test\Bundles\EventBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBundle extends Bundle
{
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

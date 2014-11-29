<?php

namespace Tomahawk\HttpCore\Middleware;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Middleware\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class StringResponse extends Middleware
{
    public function boot()
    {
        $this->getEventDispatcher()->addListener(KernelEvents::VIEW, function(GetResponseForControllerResultEvent $event) {
            if (is_string($event->getControllerResult())) {
                $event->setResponse(new Response($event->getControllerResult()));
            }
        });
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

}

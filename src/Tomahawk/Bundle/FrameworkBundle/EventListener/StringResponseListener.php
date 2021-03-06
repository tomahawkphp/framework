<?php

namespace Tomahawk\Bundle\FrameworkBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StringResponseListener implements EventSubscriberInterface
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (is_string($event->getControllerResult())) {
            $event->setResponse(new Response($event->getControllerResult()));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(array('onKernelView', 16)),
        );
    }
}

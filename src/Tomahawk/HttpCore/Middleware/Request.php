<?php

namespace Tomahawk\HttpCore\Middleware;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Middleware\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Request extends Middleware
{
    public function boot()
    {
        $this->getEventDispatcher()->addListener(KernelEvents::REQUEST, function(GetResponseEvent $event) {
            $path = $event->getRequest()->getPathInfo();

            $path = $this->formatPath($path);

            //$event->getRequest()->setU;

            //$event->getRequest()->
        });
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * Format Request Path
     *
     * @param $path
     * @return string
     */
    protected function formatPath( $path )
    {
        if ($path === '/')
        {
            return $path;
        }

        $path = '/' . trim($path, '/') . '/';

        return $path;
    }
}
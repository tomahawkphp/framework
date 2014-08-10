<?php

namespace Tomahawk\Session\Middleware;

use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Middleware\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\Session\Session as SessionManager;

class Session extends Middleware
{
    public function boot()
    {
        $session = $this->getSessionManager();

        $this->getEventDispatcher()->addListener(KernelEvents::FINISH_REQUEST, function(FinishRequestEvent $event) use ($session) {
            $session->save();
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
     * @return SessionManager
     */
    public function getSessionManager()
    {
        return $this->container->get('session');
    }
}
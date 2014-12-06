<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpCore\Middleware;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Middleware\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tomahawk\HttpCore\Response\Cookies;

class HeaderCookies extends Middleware
{
    public function boot()
    {
        $cookies = $this->getCookies();

        $this->getEventDispatcher()->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) use ($cookies) {
            foreach ($cookies->getQueued() as $cookie) {
                $event->getResponse()->headers->setCookie($cookie);
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

    /**
     * @return Cookies
     */
    public function getCookies()
    {
        return $this->container->get('cookies');
    }
}

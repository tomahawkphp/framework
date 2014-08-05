<?php

namespace Tomahawk\HttpKernel\Test\Bundles\BarBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\PhpEngine;

class BarBundle extends Bundle
{

    public function boot()
    {
        $this->container->set('bar_bundle', 'yay!');

        /*$this->getEventDispatcher()->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) {

            if ($response = $event->getResponse())
            {
                $content = $response->getContent();

                $content .= 'barbundle';

                $response->setContent($content);
                $event->setResponse($response);
            }

        });*/
    }

    public function shutdown()
    {
        $this->container->set('bar_bundle', null);
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

}
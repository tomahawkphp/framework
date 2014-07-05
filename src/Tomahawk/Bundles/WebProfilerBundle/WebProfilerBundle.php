<?php

namespace Tomahawk\Bundles\WebProfilerBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\PhpEngine;

class WebProfilerBundle extends Bundle
{

    public function boot()
    {
        $this->container->set('web_profiler', 'yay!');

        $this->getEventDispatcher()->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) {

            if ($response = $event->getResponse())
            {
                $content = $response->getContent();

                $content .= 'profiler';

                $response->setContent($content);
                $event->setResponse($response);
            }

        });
    }

    public function shutdown()
    {
        $this->container->set('web_profiler', null);
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

}
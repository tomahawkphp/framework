<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\PhpEngine;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;

class WebProfilerBundle extends Bundle
{

    public function boot()
    {
        $this->setUpProfiler();

        // Add new profiler to container

        $this->getEventDispatcher()->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) {

            if ($response = $event->getResponse()) {
                $content = $response->getContent();

                $content .= 'profiler2';

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

    protected function renderProfiler()
    {

    }

    protected function getProfiler()
    {
        return $this->container->get('web_profiler');
    }

    protected function setUpProfiler()
    {
        $this->container->set('web_profiler', function(ContainerInterface $c) {
            return new Profiler($c['templating_engine']);
        });
    }

}
<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WebProfilerBundle extends Bundle implements ContainerAwareInterface
{
    public function boot()
    {
        $assetsPath = $this->getPath() .'/Resources/assets/';

        $this->container->set('web_profiler', function(ContainerInterface $c) use ($assetsPath) {
            return new Profiler($c['templating'], $assetsPath);
        });
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
        $c = $this->container;

        $dispatcher->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) use($c) {

            if ($response = $event->getResponse()) {
                $content = $response->getContent();

                /** @var Profiler $webProfiler */
                $webProfiler = $c['web_profiler'];

                $webProfiler->setRequest($event->getRequest());

                // Check if we have the query stack from doctrine
                $debugStack = $c->has('doctrine.query_stack') ? $c->get('doctrine.query_stack') : null;

                if ($debugStack) {
                    $webProfiler->addDoctrineQueries($debugStack);
                }

                $content .= $webProfiler->render();

                $response->setContent($content);
                $event->setResponse($response);
            }

        });
    }

    public function shutdown()
    {
        $this->container->remove('web_profiler');
    }
}

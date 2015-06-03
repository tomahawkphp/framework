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
            $debugStack = $c->has('doctrine.query_stack') ? $c->get('doctrine.query_stack') : null;
            return new Profiler($c['templating'], $c->get('illuminate_database')->getDatabaseManager(), $assetsPath, $debugStack);
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

                // If we're not using Illuminate DB we don't need to do this
                if (false === $c->get('config')->get('database.enabled')) {

                    $manager = $c->get('illuminate_database')->getDatabaseManager()->connection();
                    $queryLog = $manager->getQueryLog();

                    $c['web_profiler']->addQueries($queryLog);

                }

                $content .= $c['web_profiler']->render();

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

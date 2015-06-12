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
            $databaseManager = null;

            // Check if we're using Illuminate Database
            if (true === $c['config']->get('database.enabled')) {
                $databaseManager =  $c->get('illuminate_database')->getDatabaseManager();
            }

            return new Profiler($c['templating'], $databaseManager, $assetsPath);
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
                if (true === $c->get('config')->get('database.enabled')) {

                    $manager = $c->get('illuminate_database')->getDatabaseManager()->connection();
                    $queryLog = $manager->getQueryLog();

                    $c['web_profiler']->addQueries($queryLog);
                }

                // Check if we have the query stack from doctrine
                $debugStack = $c->has('doctrine.query_stack') ? $c->get('doctrine.query_stack') : null;

                if ($debugStack) {

                    foreach ($debugStack->queries as $query) {

                        if (!$query['params']) {
                            $query['params'] = array();
                        }

                        if (!$query['types']) {
                            $query['types'] = array();
                        }

                        // Because doctrine columns can be more advanced we need to convert them to string
                        // This is a quick a dirty way of doing it so could do with going elsewhere
                        $query['params'] = $c['web_profiler']->convertDoctrineParameters($query['params'], $query['types']);

                        $queries = array(
                            array(
                                'query'    => $query['sql'],
                                'bindings' => $query['params'],
                                'time'     => $query['executionMS'],
                            )
                        );

                        $c['web_profiler']->addQueries($queries);
                    }
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

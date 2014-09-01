<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;

class WebProfilerBundle extends Bundle implements ContainerAwareInterface
{

    public function boot()
    {
        $this->setUpProfiler();

        $c = $this->container;

        $this->getEventDispatcher()->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) use($c) {

            if ($response = $event->getResponse()) {
                $content = $response->getContent();
                $manager = $c->get('illuminate_database')->getDatabaseManager()->connection();
                $queryLog = $manager->getQueryLog();

                $c['web_profiler']->addQueries($queryLog);

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

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    protected function getProfiler()
    {
        return $this->container->get('web_profiler');
    }

    protected function setUpProfiler()
    {
        $assetsPath = $this->getPath() .'/Resources/assets/';

        $this->container->set('web_profiler', function(ContainerInterface $c) use ($assetsPath) {
            return new Profiler($c['templating'], $c->get('illuminate_database')->getDatabaseManager(), $assetsPath);
        });
    }

}
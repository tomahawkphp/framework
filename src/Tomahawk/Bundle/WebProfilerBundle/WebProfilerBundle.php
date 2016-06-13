<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Tomahawk\Bundle\WebProfilerBundle\EventListener\InjectWebProfilerListener;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
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
        $dispatcher->addSubscriber(new InjectWebProfilerListener($this->container));
    }

    public function shutdown()
    {
        $this->container->remove('web_profiler');
    }
}

<?php

namespace Tomahawk\Profiler;

use Tomahawk\Profiler\EventListener\InjectWebProfilerListener;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\EventsProviderInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfilerServiceProvider implements ServiceProviderInterface, EventsProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param ContainerInterface $container An Container instance
     */
    public function register(ContainerInterface $container)
    {
        $container->set('profiler', function(ContainerInterface $c) {

            $assetsPath = __DIR__ .'/Resources/assets/';

            return new Profiler($c['templating'], $assetsPath, $c['kernel']->getStartTime());
        });
    }

    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     * @return
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber(new InjectWebProfilerListener($container));
    }

}

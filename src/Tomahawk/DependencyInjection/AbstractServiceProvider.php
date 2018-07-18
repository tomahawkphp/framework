<?php

namespace Tomahawk\DependencyInjection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractServiceProvider
 * @package Tomahawk\DependencyInjection
 */
abstract class AbstractServiceProvider implements
    ServiceProviderInterface,
    BootableProviderInterface,
    EventsProviderInterface,
    RoutesProviderInterface
{
    public function boot(ContainerInterface $container)
    {

    }

    public function register(ContainerInterface $container)
    {

    }

    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {

    }

    public function routes()
    {

    }
}

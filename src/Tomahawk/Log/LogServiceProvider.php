<?php

namespace Tomahawk\Log;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class LogServiceProvider
 * @package Tomahawk\Log
 */
class LogServiceProvider implements ServiceProviderInterface
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
        // TODO: Implement register() method.
    }
}

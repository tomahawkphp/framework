<?php

namespace Tomahawk\Log;

use Psr\Log\LoggerInterface;
use Tomahawk\Cache\Factory\StoreFactory;
use Tomahawk\Config\ConfigInterface;
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
        $container->set(LogManagerInterface::class, function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c->get(ConfigInterface::class);
            return new LogManager(
                $c->get(StoreFactory::class),
                $config->get('logging.default')
            );
        });

        $container->addAlias('logger', LogManagerInterface::class);
        $container->addAlias(LoggerInterface::class, LogManagerInterface::class);
    }
}

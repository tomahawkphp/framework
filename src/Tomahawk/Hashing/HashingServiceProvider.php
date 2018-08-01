<?php

namespace Tomahawk\Hashing;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class HashingServiceProvider
 *
 * @package Tomahawk\Hashing
 */
class HashingServiceProvider implements ServiceProviderInterface
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
        $container->set(HasherInterface::class, function(ContainerInterface $c) {
            return new Hasher();
        });

        $container->addAlias('hasher', HasherInterface::class);
    }
}

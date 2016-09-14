<?php

namespace Tomahawk\DependencyInjection\Test;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container['param'] = 'value';

        $container['service'] = function () {
            return new Service();
        };

        $container['factory'] = $container->factory(function () {
            return new Service();
        });
    }
}

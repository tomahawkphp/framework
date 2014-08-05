<?php

namespace Tomahawk\DI\Test;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;

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
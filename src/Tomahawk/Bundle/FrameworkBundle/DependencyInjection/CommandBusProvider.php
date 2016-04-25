<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DependencyInjection;

use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\CommandBus\CommandBus;
use Tomahawk\CommandBus\CommandHandlerResolver;

class CommandBusProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\CommandBus\CommandHandlerResolverInterface', function(ContainerInterface $c) {
            return new CommandHandlerResolver($c);
        });

        $container->set('Tomahawk\CommandBus\CommandBusInterface', function(ContainerInterface $c) {
            return new CommandBus($c['commandbus_handler_resolver']);
        });

        $container->addAlias('commandbus', 'Tomahawk\CommandBus\CommandBusInterface');
        $container->addAlias('commandbus_handler_resolver', 'Tomahawk\CommandBus\CommandHandlerResolverInterface');
    }
}

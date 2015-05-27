<?php

namespace Tomahawk\Bundle\CSRFBundle\DI;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\Bundle\CSRFBundle\Event\TokenSubscriber;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManager;

class CSRFProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface', function(ContainerInterface $c) {
            return new TokenManager($c['session']);
        });

        $container->addAlias('security.csrf.tokenmanager', 'Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface');
    }
}

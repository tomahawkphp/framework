<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\CSRFBundle\DependencyInjection;

use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManager;

class CSRFProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface', function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c['config'];
            return new TokenManager($c['session'], $config->get('security.csrf_token_name', '_csrf_token'));
        });

        $container->addAlias('security.csrf.tokenmanager', 'Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface');
    }
}

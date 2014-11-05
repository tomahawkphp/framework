<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\SwiftmailerBundle\DI;

use Swift_Mailer;
use Tomahawk\Bundle\SwiftmailerBundle\TransportBuilder;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;

class SwiftmailerBundleProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('mailer.transport', function(ContainerInterface $c) {
            return TransportBuilder::build($c['config']->get('swiftmailer'));
        });

        $container->set('mailer', function(ContainerInterface $c) {
            return new Swift_Mailer($c['mailer.transport']);
        });
    }
}

<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Mail;

use Swift_Mailer;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class MailServiceProvider
 * @package Tomahawk\Mail
 */
class MailServiceProvider implements ServiceProviderInterface
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

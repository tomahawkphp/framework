<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Bundle\FrameworkBundle\DI\FrameworkProvider;
use Tomahawk\Config\ConfigInterface;

class FrameworkBundle extends Bundle
{

    public function boot()
    {
        $this->container->register(new FrameworkProvider());

        $c = $this->container;

        $eventDispatcher = $c->get('event_dispatcher');

        $eventDispatcher->addSubscriber($c->get('route_listener'));

        if ($trustedProxies = $this->getConfig()->get('kernel.trusted_proxies')) {
            Request::setTrustedProxies($trustedProxies);
        }

        if ($this->getConfig()->get('kernel.http_method_override')) {
            Request::enableHttpMethodParameterOverride();
        }

        if ($trustedHosts = $this->getConfig()->get('kernel.trusted_hosts')) {
            Request::setTrustedHosts($trustedHosts);
        }
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

}

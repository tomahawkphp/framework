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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Bundle\FrameworkBundle\DI\FrameworkProvider;
use Tomahawk\Config\ConfigInterface;

class FrameworkBundle extends Bundle
{

    public function boot()
    {
        $this->container->register(new FrameworkProvider());

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
     * Register any events for the bundle
     *
     * This is called after all bundles have been boot so you get access
     * to all the services
     *
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($this->container->get('route_listener'));

        $dispatcher->addSubscriber($this->container->get('locale_listener'));
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

}

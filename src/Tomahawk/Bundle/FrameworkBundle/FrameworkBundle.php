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

use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\AuthProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CommandBusProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\ConfigProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\RoutingProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\SessionProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TranslatorProvider;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CacheProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TemplatingProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\FrameworkProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FrameworkBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new FrameworkProvider());

        $this->container->register(new AuthProvider());

        $this->container->register(new CacheProvider());

        $this->container->register(new CommandBusProvider());

        $this->container->register(new ConfigProvider());

        $this->container->register(new TemplatingProvider());

        $this->container->register(new TranslatorProvider());

        $this->container->register(new RoutingProvider());

        $this->container->register(new SessionProvider());

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

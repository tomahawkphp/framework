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

use Tomahawk\Config\ConfigInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\AuthenticationServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CommandBusServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\ConfigServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\RoutingServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\SessionServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TranslatorServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CacheServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TemplatingServiceProvider;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\FrameworkServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FrameworkBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new FrameworkServiceProvider());

        $this->container->register(new AuthenticationServiceProvider());

        $this->container->register(new CacheServiceProvider());

        $this->container->register(new CommandBusServiceProvider());

        $this->container->register(new ConfigServiceProvider());

        $this->container->register(new TemplatingServiceProvider());

        $this->container->register(new TranslatorServiceProvider());

        $this->container->register(new RoutingServiceProvider());

        $this->container->register(new SessionServiceProvider());

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

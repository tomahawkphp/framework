<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Framework;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\BootableProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\EventsProviderInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\HttpCore\Request;

/**
 * Class RequestServiceProvider
 *
 * @package Tomahawk\Framework
 */
class RequestServiceProvider implements BootableProviderInterface, ServiceProviderInterface, EventsProviderInterface
{
    /**
     * @param ContainerInterface $container An Container instance
     */
    public function boot(ContainerInterface $container)
    {
        /** @var ConfigInterface $config */
        $config = $container->get('config');

        if ($trustedProxies = $config->get('kernel.trusted_proxies')) {
            Request::setTrustedProxies($trustedProxies, Request::HEADER_X_FORWARDED_FOR);
        }

        if ($config->get('kernel.http_method_override')) {
            Request::enableHttpMethodParameterOverride();
        }

        if ($trustedHosts = $config->get('kernel.trusted_hosts')) {
            Request::setTrustedHosts($trustedHosts);
        }
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param ContainerInterface $container An Container instance
     */
    public function register(ContainerInterface $container)
    {
        // TODO: Implement register() method.
    }

    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     * @return
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber($container->get('locale_listener'));
    }
}

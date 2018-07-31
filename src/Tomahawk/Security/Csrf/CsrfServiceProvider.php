<?php

namespace Tomahawk\Security\Csrf;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\EventsProviderInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Security\Csrf\EventListener\TokenListener;
use Tomahawk\Security\Csrf\Token\TokenManager;
use Tomahawk\Security\Csrf\Token\TokenManagerInterface;

/**
 * Class CsrfServiceProvider
 *
 * @package Tomahawk\Security\Csrf
 */
class CsrfServiceProvider implements ServiceProviderInterface, EventsProviderInterface
{

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
        $container->set(TokenManagerInterface::class, function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c['config'];
            return new TokenManager($c['session'], $config->get('security.csrf_token_name', '_csrf_token'));
        });

        $container->addAlias('security.csrf.tokenmanager', TokenManagerInterface::class);
    }

    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber(new TokenListener($container->get(TokenManagerInterface::class)));
    }
}

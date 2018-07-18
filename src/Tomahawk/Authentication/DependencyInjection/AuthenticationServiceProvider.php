<?php

namespace Tomahawk\Authentication\DependencyInjection;

use Tomahawk\Authentication\AuthenticationProvider;
use Tomahawk\Authentication\Factory\GuardFactory;
use Tomahawk\Authentication\Factory\UserProviderFactory;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class AuthenticationServiceProvider
 * @package Tomahawk\Authentication\DependencyInjection
 */
class AuthenticationServiceProvider implements ServiceProviderInterface
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
        $container->set(GuardFactory::class, function(ContainerInterface $c) {

            $guardServiceIds = $c->findTaggedServiceIds('authentication.guard.factory');

            $customGuards = [];

            foreach ($guardServiceIds as $guardServiceId) {
                $customGuard = $c->get($guardServiceId);
                $customGuards[$customGuard->getName()] = $customGuard;
            }

            return new GuardFactory(
                $c,
                $c->get(UserProviderFactory::class),
                $c->get(ConfigInterface::class),
                $customGuards
            );
        });

        $container->set(UserProviderFactory::class, function(ContainerInterface $c) {

            $userProviderServiceIds = $c->findTaggedServiceIds('authentication.user_provider.factory');

            $customUserProviders = [];

            foreach ($userProviderServiceIds as $userProviderServiceId) {
                $customUserProvider = $c->get($userProviderServiceId);
                $customUserProviders[$customUserProvider->getName()] = $customUserProvider;
            }

            return new UserProviderFactory(
                $c,
                $c->get(ConfigInterface::class),
                $customUserProviders
            );
        });

        $container->set(AuthenticationProvider::class, function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c->get(ConfigInterface::class);

            return new AuthenticationProvider(
                $c->get(GuardFactory::class),
                $config->get('auth.default')
            );
        });
    }
}

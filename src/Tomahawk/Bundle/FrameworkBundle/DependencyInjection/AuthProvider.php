<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DependencyInjection;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Auth\AuthManager;
use Tomahawk\Auth\Storage\SessionStorage;
use Tomahawk\Auth\Encoder\BCryptPasswordEncoder;
use Tomahawk\Auth\User\InMemoryUserProvider;

class AuthProvider implements ServiceProviderInterface
{
    /**
     * Default user providers
     *
     * @var array
     */
    private $defaultUserProviders = [
        'memory' => 'auth.provider.memory',
    ];

    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\Auth\Encoder\PasswordEncoderInterface', function(ContainerInterface $c) {
            return new BCryptPasswordEncoder();
        });

        $container->set('Tomahawk\Auth\Storage\StorageInterface', function(ContainerInterface $c) {
            return new SessionStorage($c['session']);
        });

        $container->set('Tomahawk\Auth\User\UserProviderInterface', function(ContainerInterface $c) {

            // Get registered
            $providers = $c['config']->get('security.providers');

            // User providers
            $userProvider = $c['config']->get('security.provider');

            // Is it a default user provider
            if (isset($this->defaultUserProviders[$userProvider])) {
                $userProviderService = $this->defaultUserProviders[$userProvider];
            }
            else {

                // Its a custom one so get service id for this
                if ( ! isset($providers[$userProvider]['service'])) {
                    throw new \InvalidArgumentException(sprintf('Unknown user provider "%s". Have you added it to the security config and set the "service" parameter?', $userProvider));
                }

                $userProviderService = $providers[$userProvider]['service'];
            }

            if ( ! isset($c[$userProviderService])) {
                throw new \InvalidArgumentException(sprintf('User provider "%s" not registered under "%s"', $userProvider, $userProviderService));
            }

            return $c[$userProviderService];
        });

        $container->set('auth.provider.memory', function(ContainerInterface $c) {
            $config = $c['config']->get('security.providers.memory');

            $users = isset($config['users']) ? $config['users'] : [];

            return new InMemoryUserProvider($users);
        });

        $container->set('Tomahawk\Auth\AuthManagerInterface', function(ContainerInterface $c) {
            return new AuthManager(
                $c['auth.user.provider'],
                $c['auth.password.encoder'],
                $c['auth.storage']
            );
        });

        $container->addAlias('auth.storage', 'Tomahawk\Auth\Storage\StorageInterface');
        $container->addAlias('auth.password.encoder', 'Tomahawk\Auth\Encoder\PasswordEncoderInterface');
        $container->addAlias('auth.password.encoder.bcrypt', 'Tomahawk\Auth\Encoder\PasswordEncoderInterface');
        $container->addAlias('auth.user.provider', 'Tomahawk\Auth\User\UserProviderInterface');
        $container->addAlias('auth', 'Tomahawk\Auth\AuthManagerInterface');
    }
}

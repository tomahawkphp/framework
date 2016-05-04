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
use Tomahawk\Authentication\AuthenticationProvider;
use Tomahawk\Authentication\Storage\SessionStorage;
use Tomahawk\Authentication\Encoder\BCryptPasswordEncoder;
use Tomahawk\Authentication\User\InMemoryUserProvider;

class AuthenticationServiceProvider implements ServiceProviderInterface
{
    /**
     * Default user providers
     *
     * @var array
     */
    private $defaultUserProviders = [
        'memory' => 'authentication.provider.memory',
    ];

    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\Authentication\Encoder\PasswordEncoderInterface', function(ContainerInterface $c) {
            return new BCryptPasswordEncoder();
        });

        $container->set('Tomahawk\Authentication\Storage\StorageInterface', function(ContainerInterface $c) {
            return new SessionStorage($c['session']);
        });

        $container->set('Tomahawk\Authentication\User\UserProviderInterface', function(ContainerInterface $c) {

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

        $container->set('authentication.provider.memory', function(ContainerInterface $c) {
            $config = $c['config']->get('security.providers.memory');

            $users = isset($config['users']) ? $config['users'] : [];

            return new InMemoryUserProvider($users);
        });

        $container->set('Tomahawk\Authentication\AuthenticationProviderInterface', function(ContainerInterface $c) {
            return new AuthenticationProvider(
                $c['authentication.user.provider'],
                $c['authentication.password.encoder'],
                $c['authentication.storage']
            );
        });

        $container->addAlias('authentication.storage', 'Tomahawk\Authentication\Storage\StorageInterface');
        $container->addAlias('authentication.password.encoder', 'Tomahawk\Authentication\Encoder\PasswordEncoderInterface');
        $container->addAlias('authentication.password.encoder.bcrypt', 'Tomahawk\Authentication\Encoder\PasswordEncoderInterface');
        $container->addAlias('authentication.user.provider', 'Tomahawk\Authentication\User\UserProviderInterface');
        $container->addAlias('authentication', 'Tomahawk\Authentication\AuthenticationProviderInterface');
    }
}

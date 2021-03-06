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

use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Config\Loader\PhpConfigLoader;
use Tomahawk\Config\Loader\YamlConfigLoader;
use Tomahawk\Config\ConfigManager;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('Symfony\Component\Config\Loader\LoaderInterface', function(ContainerInterface $c) {

            $kernel = $c['kernel'];
            $defaultPath = $kernel->getRootDir() .'/Resources/config';

            $locator = new FileLocator(array($defaultPath));

            $loaderResolver = new LoaderResolver(
                array(
                    new YamlConfigLoader($locator),
                    new PhpConfigLoader($locator)
                )
            );

            return new DelegatingLoader($loaderResolver);
        });

        $container->set('Tomahawk\Config\ConfigInterface', function(ContainerInterface $c) {

            $kernel = $c['kernel'];

            $cacheFile = sprintf($kernel->getRootDir(). '/config/config_%s.php', $kernel->getEnvironment());

            $paths = array(
                $kernel->getRootDir() .'/config',
            );

            // Check if we have an environment config
            if (file_exists($kernel->getRootDir() .'/config/' . $kernel->getEnvironment())) {
                $paths[] = $kernel->getRootDir() .'/config/' . $kernel->getEnvironment();
            }

            $config = new ConfigManager($c['config_loader'], $paths, $cacheFile);
            $config->load();

            return $config;
        });

        $container->addAlias('config_loader', 'Symfony\Component\Config\Loader\LoaderInterface');
        $container->addAlias('config', 'Tomahawk\Config\ConfigInterface');
    }
}

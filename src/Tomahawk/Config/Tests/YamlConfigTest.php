<?php

namespace Tomahawk\Config\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Config\ConfigManager;
use Tomahawk\Config\Loader\YamlConfigLoader;
use Tomahawk\Config\Loader\PhpConfigLoader;
use Symfony\Component\Config\FileLocator;
//use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class YamlConfigTest extends TestCase
{
    public function testYaml2()
    {
        $configDirectories = array(__DIR__.'/Resources/configs');

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $this->assertTrue($delegatingLoader->supports(__DIR__.'/Resources/configs/database.yml'));

        $delegatingLoader->load(__DIR__.'/Resources/configs/database.yml');
    }

    public function testCache()
    {
        $configDirectories = array(__DIR__.'/Resources/configs');

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $cachePath = __DIR__.'/Resources/cache/config.php';

        $configCache = new ConfigCache($cachePath, true);

        $configFilePath = $locator->locate($cachePath);

        $resources = array();

        if ( ! $configCache->isFresh())
        {

            $code = $delegatingLoader->load($locator->locate('auth.php'));

            $resources[] = new FileResource($configFilePath);
            // serialize the config array and save it
            $configCache->write(serialize($code), $resources);

        }
    }

}

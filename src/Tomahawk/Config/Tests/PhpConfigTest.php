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

class PhpConfigTest extends TestCase
{
    public function testConfigLoader()
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

        $this->assertTrue($delegatingLoader->supports(__DIR__.'/Resources/configs/templating.php'));
        
        $ret = $delegatingLoader->load(__DIR__.'/Resources/configs/templating.php');

        $this->assertArrayHasKey('charset', $ret);
    }

}

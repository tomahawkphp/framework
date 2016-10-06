<?php

namespace Tomahawk\Config\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Tomahawk\Test\TestCase;
use Tomahawk\Config\ConfigManager;
use Tomahawk\Config\Loader\YamlConfigLoader;
use Tomahawk\Config\Loader\PhpConfigLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

class ConfigTest extends TestCase
{
    public function testSingleDirectoryOfConfigs()
    {
        $configDirectories = array(__DIR__ .'/Resources/configs');
        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->load();

        $this->assertCount(3, $config->get());

        $this->assertEquals('pdo', $config->get('auth.driver'));
        $this->assertEquals('database', $config->get('session.driver'));

        $config->set('auth.driver', 'bar');

        $this->assertEquals('bar', $config->get('auth.driver'));

        $config->set('new.foo', 'bar');

        $this->assertEquals(null, $config->get('nonexistant'));
        $this->assertEquals('bar', $config->get('new.foo'));
    }

    public function testMultipleDirectoryOfConfigs()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs',
            __DIR__ .'/Resources/configs/develop',
        );

        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->load();

        $this->assertEquals('eloquent', $config->get('auth.driver'));
        $this->assertEquals('cookie', $config->get('session.driver'));
    }

    public function testEnvConfigInheritsParent()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs',
            __DIR__ .'/Resources/configs/develop',
        );

        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->load();

        $this->assertEquals('UTF-8', $config->get('templating.charset'));
        $this->assertFalse($config->get('templating.twig.cache'));
    }

    public function testConfigHasCompiledConfig()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs',
            __DIR__ .'/Resources/configs/develop',
        );

        $cacheFile = __DIR__ .'/Resources/compiledconfigs/config_prod.php';

        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories, $cacheFile);

        $config->load();

        $this->assertEquals('eloquent', $config->get('auth.driver'));
    }

    public function testCompiledConfigIsIgnored()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/compiledconfigs',
        );

        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->load();

        $this->assertEquals('pdo', $config->get('auth.driver'));
    }

    public function testCompiledConfigIsIgnoredOnForce()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/compiledconfigs',
        );

        $cacheFile = __DIR__ .'/Resources/compiledconfigs/config_prod.php';

        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories, $cacheFile);

        $config->load(true);

        $this->assertEquals('pdo', $config->get('auth.driver'));
    }

    public function testHas()
    {
        $configDirectories = array(__DIR__ .'/Resources/configs');
        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->set(null, array(
            'foo' => array(
                'bar' => 'baz'
            )
        ));

        $this->assertTrue($config->has('foo.bar'));
        $this->assertFalse($config->has('foo.test'));
    }

    public function testArraySetAndGet()
    {
        $configDirectories = array(__DIR__ .'/Resources/configs');
        $loader = $this->getLoader($configDirectories);

        $config = $this->getConfig($loader, $configDirectories);

        $config->set(null, array(
            'foo' => array(
                'bar' => 'baz'
            )
        ));

        $this->assertEquals(array(
            'bar' => 'baz'
        ), $config->get('foo'));
        $this->assertEquals('baz', $config->get('foo.bar'));
        $this->assertEquals(array(
            'foo' => array(
                'bar' => 'baz'
            )
        ), $config->get());
    }

    protected function getLoader(array $configDirectories)
    {
        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        return $delegatingLoader;
    }

    /**
     * @param LoaderInterface $loader
     * @param array $configDirectories
     * @param null $cacheFile
     * @return ConfigManager
     */
    protected function getConfig(LoaderInterface $loader, array $configDirectories, $cacheFile = null)
    {
        $config = new ConfigManager($loader, $configDirectories, $cacheFile);

        return $config;
    }
}

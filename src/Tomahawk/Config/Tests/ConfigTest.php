<?php

namespace Tomahawk\Config\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Config\ConfigManager;
use Tomahawk\Config\Loader\YamlConfigLoader;
use Tomahawk\Config\Loader\PhpConfigLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class ConfigTest extends TestCase
{
    /**
     * @var \Tomahawk\Config\ConfigManager
     */
    protected $config;

    public function setUp()
    {
        $configDirectories = array(__DIR__ .'/Resources/configs');

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $this->config = new ConfigManager($delegatingLoader, $configDirectories);
    }

    public function tearDown()
    {
        $this->config = null;
    }

    public function testGetAll()
    {
        $this->config->load();

        $this->assertCount(2, $this->config->get());
    }

    public function testSingleConfig()
    {
        $config = $this->config;

        $config->load();

        $this->assertEquals('pdo', $config->get('auth.driver'));
        $this->assertEquals('database', $config->get('session.driver'));

        $config->set('auth.driver', 'bar');

        $this->assertEquals('bar', $config->get('auth.driver'));
    }


    public function testEnvConfig()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs',
            __DIR__ .'/Resources/configs/develop'
        );

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);

        $config->load();

        $this->assertEquals('eloquent', $config->get('auth.driver'));
        $this->assertEquals('cookie', $config->get('session.driver'));
    }

    public function testChangeTheThings()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs'
        );

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);

        $config->load();

        $this->assertEquals('pdo', $config->get('auth.driver'));
        $this->assertEquals('database', $config->get('session.driver'));

        $config->set('auth.driver', 'bar');

        $this->assertEquals('bar', $config->get('auth.driver'));
    }

    public function testSetNewValue()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs',
            __DIR__ .'/Resources/configs/develop'
        );

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);
        $config->load();

        $config->set('new.foo', 'bar');

        $this->assertEquals('bar', $config->get('new.foo'));

    }

    public function testgetNonExistantValue()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs'
        );

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);
        $config->load();

        $config->load();

        $this->assertEquals(null, $config->get('new'));

    }

    public function testOverrideConfig()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs'
        );

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(
            array(
                new YamlConfigLoader($locator),
                new PhpConfigLoader($locator)
            )
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);
        $config->load();

        $config->load();

        $this->assertEquals(null, $config->get('new'));

    }

    public function testArraySetAndGet()
    {
        $configDirectories = array(
            __DIR__ .'/Resources/configs'
        );

        $loaderResolver = new LoaderResolver(array());

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $config = new ConfigManager($delegatingLoader, $configDirectories);

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
}

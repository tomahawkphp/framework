<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CacheProvider;

class CacheProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\CacheProvider
     */
    public function testProvider()
    {
        $config = $this->getConfig();
        $config
            ->method('get')
            ->will($this->returnValueMap(array(
                array('cache.namespace', ''),
                array('cache.directory', null, __DIR__ . '/Resources/cache'),
                array('cache.directory', '', __DIR__ . '/Resources/cache'),
            )));

        $container = new Container();
        $container->set('config', $config);

        $cacheProvider = new CacheProvider();
        $cacheProvider->register($container);

        $this->assertInstanceOf('Tomahawk\Cache\Provider\ArrayProvider', $container->get('cache.providers.array'));
        $this->assertInstanceOf('Tomahawk\Cache\Provider\ApcuProvider', $container->get('cache.providers.apcu'));
        $this->assertInstanceOf('Tomahawk\Cache\Provider\XcacheProvider', $container->get('cache.providers.xcache'));
        $this->assertInstanceOf('Tomahawk\Cache\Provider\FilesystemProvider', $container->get('cache.providers.filesystem'));
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');
        return $config;
    }
}

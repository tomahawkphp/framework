<?php

namespace Tomahawk\Cache\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\CacheManagerInterface;
use Tomahawk\Cache\Factory\StoreFactory;
use Tomahawk\Cache\Test\Factory\TestStoreFactory;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Cache\DependencyInjection\CacheServiceProvider;

class CacheServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.default', 'array', 'array'],
            ])
        ;

        $container->set(ConfigInterface::class, $configManger);
        $container->set(TestStoreFactory::class, new TestStoreFactory());
        $container->tag(TestStoreFactory::class, 'cache.store.factory');

        $provider = new CacheServiceProvider();
        $provider->register($container);

        $this->assertTrue($container->has(StoreFactory::class));
        $this->assertInstanceOf(StoreFactory::class, $container->get(StoreFactory::class));

        $this->assertTrue($container->has(CacheManagerInterface::class));
        $this->assertInstanceOf(CacheManagerInterface::class, $container->get(CacheManagerInterface::class));
    }
}

<?php

namespace Tomahawk\Cache\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Tomahawk\Cache\Factory\StoreFactory;
use Tomahawk\Cache\Store\ArrayStore;
use Tomahawk\Cache\Store\FilesystemStore;
use Tomahawk\Cache\Store\MemcachedStore;
use Tomahawk\Cache\Store\RedisStore;
use Tomahawk\Cache\Test\Factory\TestStoreFactory;
use Tomahawk\Cache\Test\Store\TestStore;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;

class StoreFactoryTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cache driver [thing] is not defined.
     */
    public function testInvalidStoreThrowsException()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.array', null, null],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $factory->make('thing');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cache driver [default] is not defined.
     */
    public function testStoreWithNoCreatorThrowsException()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.default', null, [
                    'driver' => 'custom',
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $factory->make('default');
    }

    public function testArrayStore()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.array', null, [
                    'driver' => 'array',
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $store = $factory->make('array');

        $this->assertInstanceOf(ArrayStore::class, $store);
    }

    public function testFilesystemStore()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.filesystem', null, [
                    'driver' => 'filesystem',
                    'directory' => __DIR__ .'/../Test/storage/cache',
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $store = $factory->make('filesystem');

        $this->assertInstanceOf(FilesystemStore::class, $store);
    }

    public function testRedisStore()
    {
        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.redis', null, [
                    'driver' => 'redis',
                    'connection' => 'default',
                ]],
                ['database.redis', null, [
                    'cluster' => false,

                    'default' => [
                        'host' => '127.0.0.1',
                        'password' => null,
                        'port' => 6379,
                        'database' => 0,
                    ],

                    'options' => [
                        'parameters' => [ // Parameters provide defaults for the Connection Factory
                            'password' => null, // Redirects need PW for the other nodes
                            'scheme' => 'tcp',  // Redirects also must match scheme
                        ],
                        'ssl' => ['verify_peer' => false], // Since we dont have TLS cert to verify
                    ],
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $store = $factory->make('redis');

        $this->assertInstanceOf(RedisStore::class, $store);
    }

    public function testMemcachedStore()
    {
        if ( ! class_exists('\Memcached')) {
            $this->markTestSkipped('Memcached is not installed');
        }

        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.memcached', null, [
                    'driver' => 'memcached',
                    'persistent_id' => 'memcached_id',
                    'sasl' => [
                        'memcached_username',
                        'memcached_password',
                    ],
                    'options' => [
                        \Memcached::OPT_CONNECT_TIMEOUT  => 2000,
                    ],
                    'servers' => [
                        [
                            'host' => '127.0.0.1',
                            'port' => 11211,
                            'weight' => 100,
                        ],
                    ],
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $store = $factory->make('memcached');

        $this->assertInstanceOf(MemcachedStore::class, $store);
    }

    public function testMemcachedStoreWithNoId()
    {
        if ( ! class_exists('\Memcached')) {
            $this->markTestSkipped('Memcached is not installed');
        }

        $container = new Container();

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.memcached', null, [
                    'driver' => 'memcached',
                    'persistent_id' => null,
                    'sasl' => [
                        'memcached_username',
                        'memcached_password',
                    ],
                    'options' => [
                        // Memcached::OPT_CONNECT_TIMEOUT  => 2000,
                    ],
                    'servers' => [
                        [
                            'host' => '127.0.0.1',
                            'port' => 11211,
                            'weight' => 100,
                        ],
                    ],
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger);

        $store = $factory->make('memcached');

        $this->assertInstanceOf(MemcachedStore::class, $store);
    }

    public function testCustomStore()
    {
        $container = new Container();

        $testStoreFactory = new TestStoreFactory();

        $customFactories = [
            $testStoreFactory->getName() => $testStoreFactory,
        ];

        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['cache.prefix', null, 'tomahawk'],
                ['cache.stores.test', null, [
                    'driver' => 'test',
                ]],
            ])
        ;

        $factory = new StoreFactory($container, $configManger, $customFactories);

        $store = $factory->make('test');

        $this->assertInstanceOf(TestStore::class, $store);
    }
}

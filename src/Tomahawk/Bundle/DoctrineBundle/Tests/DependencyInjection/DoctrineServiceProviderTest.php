<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\DI;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\DoctrineBundle\DependencyInjection\DoctrineServiceProvider;

class DoctrineServiceProviderTest extends TestCase
{
    /**
     * @var array
     */
    protected $doctrineConfig = [
        'cache' => 'array',
        'format'    => 'xml',
        'proxy_namespace' => 'DoctrineProxies',
        'auto_generate_proxies' => true,
        'proxy_directories' => __DIR__ . '/../Resources/Doctrine/proxies',
        'mapping_directories' => [__DIR__ . '/../Resources/Doctrine/mappings'],
        'default_connection' => 'default',
        'connections' => [
            'default' => [
                'service'      => 'doctrine.connection.default',
                'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                'driver'       => 'pdo_mysql',
                'master'       => [
                    'host'      => 'localhost',
                    'port'      => '3306',
                    'dbname'    => 'tomahawk',
                    'user'      => 'root',
                    'password'  => '',
                ],
                'slaves' => [
                    [
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'dbname'    => 'tomahawk',
                        'user'      => 'root',
                        'password'  => '',
                    ]
                ],
            ]
        ],
    ];

    /**
     * @var array
     */
    protected $securityConfig = [
        'user_class' => 'My\User',
        'username'   => 'username',
    ];

    public function testProviderAddsDoctrineToContainer()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();

        $config->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['doctrine', null, $this->doctrineConfig],
                ['security.providers.doctrine', null, $this->securityConfig],
                ['cache.namespace', null, ''],
            ]);


        $container->set('config', $config);
        $container->set('kernel', $this->getKernel());

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertTrue($container->has('doctrine'));
        $this->assertTrue($container->has('doctrine.entitymanager'));
        $this->assertTrue($container->has('doctrine.query_stack'));
        $this->assertTrue($container->has('authentication.provider.doctrine'));


        $this->assertInstanceOf('Doctrine\DBAL\Logging\DebugStack', $container->get('doctrine.query_stack'));
        $this->assertInstanceOf('Tomahawk\Bundle\DoctrineBundle\Registry', $container->get('doctrine'));
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $container->get('doctrine.entitymanager'));
        $this->assertInstanceOf('Tomahawk\Authentication\User\UserProviderInterface', $container->get('authentication.provider.doctrine'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDefaultConnectionsThrowsException()
    {
        $container = $this->getContainer();

        $doctrine = $this->doctrineConfig;
        $doctrine['default_connection'] = 'invalid';

        $config = $this->getConfig();
        $config->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($doctrine));

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $container->get('doctrine.connection.default');
    }

    public function testDefaultConnectionsIsReturned()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();
        $config->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($this->doctrineConfig));


        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf('Doctrine\DBAL\Connection', $container->get('doctrine.connection.default'));
    }

    public function testConnectionsArrayIsReturned()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();
        $config->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($this->doctrineConfig));

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $connections = $container->get('doctrine.connections');

        $this->assertTrue(is_array($connections));
    }

    public function testCache()
    {
        $doctrineConfig = $this->doctrineConfig;

        $container = $this->getContainer();

        $config = $this->getConfig();

        $config->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['doctrine', null, $doctrineConfig],
                ['cache.namespace', null, ''],
            ]);

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf('Doctrine\Common\Cache\CacheProvider', $container->get('doctrine.cache'));
    }

    public function testAPCUCacheInstanceIsReturned()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();

        $config->expects($this->once())
            ->method('get')
            ->with('cache.namespace')
            ->will($this->returnValue(''));

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf('Doctrine\Common\Cache\ApcuCache', $container->get('doctrine.cache.apcu'));
    }

    public function testArrayacheInstanceIsReturned()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();

        $config->expects($this->once())
            ->method('get')
            ->with('cache.namespace')
            ->will($this->returnValue(''));

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $container->get('doctrine.cache.array'));
    }

    public function testFSacheInstanceIsReturned()
    {
        $folder = dirname(__FILE__) . '/../cache';

        $container = $this->getContainer();

        $config = $this->getConfig();

        $config->expects($this->at(0))
            ->method('get')
            ->with('cache.directory')
            ->will($this->returnValue($folder));

        $config->expects($this->at(1))
            ->method('get')
            ->with('cache.namespace')
            ->will($this->returnValue(''));

        $container->set('config', $config);

        $provider = new DoctrineServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf('Doctrine\Common\Cache\FilesystemCache', $container->get('doctrine.cache.filesystem'));
    }

    protected function getContainer()
    {
        return new Container();
    }

    protected function getConfig()
    {
        return $this->getMock('Tomahawk\Config\ConfigInterface');
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('prod'));

        $kernel->expects($this->any())
            ->method('isDebug')
            ->will($this->returnValue(false));

        return $kernel;
    }

}

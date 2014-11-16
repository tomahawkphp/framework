<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\DI\DoctrineProvider;

class DoctrineBundleProviderTest extends TestCase
{
    protected $doctrineConfig;

    protected function setUp()
    {
        $this->doctrineConfig = array(
            'cache' => 'array',
            'format'    => 'xml',
            'proxy_namespace' => 'DoctrineProxies',
            'auto_generate_proxies' => true,
            'proxy_directories' => __DIR__ . '/../Resources/Doctrine/proxies',
            'mapping_directories' => array(__DIR__ . '/../Resources/Doctrine/mappings'),
            'default_connection' => 'default',
            'connections' => array(
                'default' => array(
                    'service'      => 'doctrine.connection.default',
                    'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                    'driver'       => 'pdo_mysql',
                    'master'       => array(
                        'host'      => 'localhost',
                        'port'      => '3306',
                        'dbname'    => 'tomahawk',
                        'user'      => 'root',
                        'password'  => '',
                    ),
                    'slaves' => array(
                        array(
                            'host'      => 'localhost',
                            'port'      => '3306',
                            'dbname'    => 'tomahawk',
                            'user'      => 'root',
                            'password'  => '',
                        )
                    ),
                )
            ),
        );
    }

    public function testProviderAddsDoctrineToContainer()
    {
        $container = $this->getContainer();

        $config = $this->getConfig();


        $container->set('config', $config);

        $provider = new DoctrineProvider();
        $provider->register($container);

        $this->assertTrue($container->has('doctrine'));
        $this->assertTrue($container->has('doctrine.entitymanager'));


        //$this->assertInstanceOf('Tomahawk\Bundle\DoctrineBundle\Registry', $container->get('doctrine'));
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

        $provider = new DoctrineProvider();
        $provider->register($container);

        $connections = $container->get('doctrine.connections');

        $this->assertTrue(is_array($connections));
    }

    protected function getContainer()
    {
        return new Container();
    }

    protected function getConfig()
    {
        return $this->getMock('Tomahawk\Config\ConfigInterface');
    }

}

<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\DI\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\DI\DoctrineProvider;

class DoctrineBundleProviderTest extends TestCase
{
    public function testProviderAddsDoctrineToContainer()
    {
        $container = $this->getContainer();

        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        /*$config->expects($this->once())
            ->method('get')
            ->with('cache.namespace')
            ->will($this->returnValue(''));

        $config->expects($this->once())
            ->method('get')
            ->with('cache.namespace')
            ->will($this->returnValue(''));


        $config->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue(array(
                'format'    => 'xml',
                'proxy_namespace' => 'DoctrineProxies',
                'auto_generate_proxies' => true,
                'proxy_directories' => __DIR__ . '/../Resources/Doctrine/proxies',
                'mapping_directories' => array(__DIR__ . '/../Resources/Doctrine/mappings'),
                'database' => array(
                    'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                    'driver'    => 'pdo_mysql',
                    'master' => array(
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
                ),
            )));*/

        $container->set('config', $config);

        $provider = new DoctrineProvider();
        $provider->register($container);

        $this->assertTrue($container->has('doctrine'));
        $this->assertTrue($container->has('doctrine.entitymanager'));


        //$this->assertInstanceOf('Tomahawk\Bundle\DoctrineBundle\Registry', $container->get('doctrine'));
        //$this->assertInstanceOf('Swift_Mailer', $container->get('mailer'));
    }

    protected function getContainer()
    {
        return new Container();
    }

}

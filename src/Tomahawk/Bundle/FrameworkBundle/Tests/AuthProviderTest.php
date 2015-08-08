<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\AuthProvider;

class AuthProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\AuthProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();
        $authProvider = new AuthProvider();
        $authProvider->register($container);

        $this->assertTrue($container->has('auth_handler'));
        $this->assertTrue($container->has('eloquent_auth_handler'));
        $this->assertTrue($container->has('database_auth_handler'));
        $this->assertTrue($container->has('Tomahawk\Auth\AuthInterface'));
        $this->assertTrue($container->hasAlias('auth'));


        $this->assertInstanceOf('Tomahawk\Auth\Auth', $container->get('auth'));
        $this->assertInstanceOf('Tomahawk\Auth\Handlers\DatabaseAuthHandler', $container->get('database_auth_handler'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('config', $this->getConfig());
        $container->set('illuminate_database', $this->getIlluminateDBMock());
        $container->set('hasher', $this->getMock('Tomahawk\Hashing\HasherInterface'));
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));

        return $container;
    }

    protected function getIlluminateDBMock()
    {
        $connection = $this->getMock('Illuminate\Database\ConnectionInterface');

        $manager = $this->getMockBuilder('Illuminate\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->any())
            ->method('connection')
            ->will($this->returnValue($connection));

        $db = $this->getMockBuilder('Illuminate\Database\Capsule\Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('getDatabaseManager'))
            ->getMock();

        $db->expects($this->any())
            ->method('getDatabaseManager')
            ->will($this->returnValue($manager));

        return $db;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('security.handler', null, 'eloquent'),
                array('security.handlers.eloquent', null, array(
                    'model' => 'User'
                )),
                array('security.handlers.database', null, array(
                    'table' => 'users',
                    'key'   => 'id',
                    'username'   => 'username',
                    'password'   => 'password',
                    'connection' => 'default',
                )),
            )));

        return $config;
    }
}

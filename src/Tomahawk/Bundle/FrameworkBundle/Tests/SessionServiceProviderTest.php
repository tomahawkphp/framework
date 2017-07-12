<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\SessionServiceProvider as SessionProvider;

class SessionServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\SessionServiceProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();
        $sessionProvider = new SessionProvider();
        $sessionProvider->register($container);

        $this->assertInstanceOf('Tomahawk\Session\Session', $container->get('session'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.file'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.cookie'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.database'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.null'));
    }

    /**
     * @expectedException \Exception
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\SessionServiceProvider
     */
    public function testProviderNoDriver()
    {
        $container = $this->getContainer('mongo');
        $sessionProvider = new SessionProvider();
        $sessionProvider->register($container);

        $this->assertInstanceOf('Tomahawk\Session\Session', $container->get('session'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.file'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.cookie'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage', $container->get('session.storage.database'));
    }

    protected function getContainer($sessionDriver = 'array')
    {
        $container = new Container();
        $container->set('config', $this->getConfig($sessionDriver));

        return $container;
    }

    protected function getDatabaseManager()
    {
        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getPdo')
            ->will($this->returnValue(null));

        $database = $this->getMockBuilder('Tomahawk\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        $database->expects($this->any())
            ->method('connection')
            ->will($this->returnValue($connection));

        return $database;

    }

    protected function getConfig($sessionDriver)
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('session.driver', 'array', $sessionDriver),
                array('session', null, array(
                    'driver'           => 'cookie',
                    'enabled' => true,
                    'session_name'     => 'tomahawk_session',
                    'directory'        => __DIR__ .'/Resources/sessions',
                    'cookie_name'      => 'tomahawk_cookie',
                    'cookie_lifetime'  => '',
                    'cookie_path'      => '/',
                    'cookie_domain'    => 'localhost',
                    'cookie_secure'    => true,
                    'cookie_http_only' => true,

                    // Database specific
                    'table'             => 'tomahawk_sessions',
                    'id_column'         => 'id',
                    'data_column'       => 'data',
                    'date_column'       => 'date',

                    'dsn'               => 'mysql:dbname=testdb;host=127.0.0.1',
                    'db_username'       => 'username',
                    'db_password'       => 'password',

                )),
            )));

        return $config;
    }
}

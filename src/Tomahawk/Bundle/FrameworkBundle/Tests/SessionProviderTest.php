<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\SessionProvider;

class SessionProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\SessionProvider
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
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('config', $this->getConfig());
        $container->set('database', $this->getDatabaseManager());

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

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('session.driver', 'array', 'array'),
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
                    'table'         => 'tomahawk_sessions',
                    'id_column'     => 'id',
                    'data_column'   => 'data',
                    'date_column'   => 'date'
                )),
            )));

        return $config;
    }
}

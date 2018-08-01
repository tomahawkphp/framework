<?php

namespace Tomahawk\Session;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\EventListener\SessionListener;
use Tomahawk\DependencyInjection\EventsProviderInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Class SessionServiceProvider
 *
 * @package Tomahawk\Session
 */
class SessionServiceProvider implements ServiceProviderInterface, EventsProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('session.storage.file', function(ContainerInterface $c) {
            $config = $c['config']->get('session');
            $nativeFileSessionHandler = new NativeFileSessionHandler($config['directory']);
            return new NativeSessionStorage(array(), $nativeFileSessionHandler);
        });

        $container->set('session.storage.null', function(ContainerInterface $c) {
            return new NativeSessionStorage(array(), new NullSessionHandler());
        });

        $container->set('session.storage.array', function(ContainerInterface $c) {
            return new MockArraySessionStorage();
        });

        $container->set('session.storage.cookie', function(ContainerInterface $c) {

            $config = $c['config']->get('session');

            $session_settings = array(
                'id'   	   => $config['session_name'],
                'name' 	   => $config['cookie_name'],
                'lifetime' => $config['cookie_lifetime'],
                'path'     => $config['cookie_path'],
                'domain'   => $config['cookie_domain'],
                'secure'   => $config['cookie_secure'],
                'httponly' => $config['cookie_http_only'],
            );

            return new NativeSessionStorage($session_settings);
        });

        $container->set('session.storage.database', function(ContainerInterface $c) {

            $config = $c['config']->get('session');

            $pdoSessionHandler = new PdoSessionHandler($config['dsn'], array(
                'db_table'    => $config['table'],
                'db_id_col'   => $config['id_column'],
                'db_data_col' => $config['data_column'],
                'db_time_col' => $config['date_column'],
                'db_username' => $config['db_username'],
                'db_password' => $config['db_password'],
            ));

            $session_settings = array(
                'id'   	   => $config['session_name'],
                'name' 	   => $config['cookie_name'],
                'lifetime' => $config['cookie_lifetime'],
                'path'     => $config['cookie_path'],
                'domain'   => $config['cookie_domain'],
                'secure'   => $config['cookie_secure'],
                'httponly' => $config['cookie_http_only'],
            );

            return new NativeSessionStorage($session_settings, $pdoSessionHandler);
        });

        $container->set(SessionInterface::class, function(ContainerInterface $c) {
            $session = $c['config']->get('session.driver','array');
            if ( ! $c->has('session.storage.' .$session)) {
                throw new \Exception(sprintf('Session storage %d does not exist or has not been set.', $session));
            }
            return new Session($c['session.storage.' .$session]);
        });

        $container->addAlias('session', SessionInterface::class);
        $container->addAlias('session.storage.filesystem', 'session.storage.file');
    }

    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     * @return
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber(new SessionListener($container));
    }
}

<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DI;

use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('session.storage.file', function(ContainerInterface $c) {
            $config = $c['config']->get('session');
            $nativeFileSessionHandler = new NativeFileSessionHandler($config['directory']);
            return new NativeSessionStorage(array(), $nativeFileSessionHandler);
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

            $pdo = $c['database']->connection()->getPdo();

            $config = $c['config']->get('session');

            $pdoSessionHandler = new PdoSessionHandler($pdo, array(
                'db_table'    => $config['table'],
                'db_id_col'   => $config['id_column'],
                'db_data_col' => $config['data_column'],
                'db_time_col' => $config['date_column'],
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

        $container->set('Tomahawk\Session\SessionInterface', function(ContainerInterface $c) {
            $session = $c['config']->get('session.driver','array');
            if (!$c->has('session.storage.' .$session)) {
                throw new \Exception(sprintf('Session storage %d does not exist or has not been set.', $session));
            }
            return new Session($c['session.storage.' .$session]);
        });

        $container->addAlias('session', 'Tomahawk\Session\SessionInterface');
        $container->addAlias('session.storage.filesystem', 'session.storage.file');
    }
}

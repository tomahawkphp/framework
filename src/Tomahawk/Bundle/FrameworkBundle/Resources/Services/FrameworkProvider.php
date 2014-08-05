<?php

namespace Tomahawk\Bundle\FrameworkBundle\Resources\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Tomahawk\Config\ConfigManager;
use Tomahawk\HttpCore\ResponseBuilder;
use Tomahawk\Cache\CacheManager;
use Tomahawk\Encryption\Crypt;
use Tomahawk\Database\DatabaseManager;
use Tomahawk\HttpKernel\Config\FileLocator;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Session\Session;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\Routing\Router;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Tomahawk\Forms\FormsManager;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\View\ViewGenerator;

class FrameworkProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $this->registerServices($container);
        $this->registerAliases($container);
    }

    protected function registerServices(ContainerInterface $container)
    {
        $manager = new Manager();
        $manager->addConnection(array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => '3306',
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ));

        $manager->bootEloquent();
        $manager->setAsGlobal();

        $container->set('Symfony\Component\EventDispatcherInterface', new EventDispatcher());

        $container->set('Tomahawk\Asset\AssetManagerInterface', function(ContainerInterface $container) {
            return new AssetManager($container['html_builder']);
        });

        $container->set('Tomahawk\Cache\CacheInterface', function(ContainerInterface $c) {

            $provider = $c['config']->get('cache.driver');

            return new CacheManager($c['cache.providers.' .$provider]);
        });

        $container->set('Tomahawk\Database\DatabaseManager', new DatabaseManager($manager->getDatabaseManager()));

        $container->set('Tomahawk\Encryption\CryptInterface', new Crypt(str_repeat('a', 32)));

        $container->set('Tomahawk\Forms\FormsManagerInterface', new FormsManager());

        $container->set('Tomahawk\Html\HtmlBuilderInterface', new HtmlBuilder());

        $container->set('Tomahawk\HttpCore\ResponseBuilderInterface', new ResponseBuilder());

        $container->set('Tomahawk\HttpCore\Response\CookiesInterface', $container->factory(function(ContainerInterface $c) {
            return new Cookies($c['request'], array());
        }));

        $container->set('Tomahawk\View\ViewGeneratorInterface', $container->factory(function(ContainerInterface $c) {

            //return new Cookies($c['request'], array());
        }));

        //$routeListener = new RouterListener($matcher, $context, null, $request_stack);

        $container->set('monolog_logger', function() {
            return null;
        });

        $container->set('route_listener', function(ContainerInterface $c) {
            return new RouterListener($c['url_matcher'], $c['request_context'], $c['monolog_logger'], $c['request_stack']);
        });

        $container->set('route_locator', $container->factory(function(ContainerInterface $c) {
            return new FileLocator(array(__DIR__ .'/../app/Resources/'));
        }));

        $container->set('route_loader', $container->factory(function(ContainerInterface $c) {
            return new PhpFileLoader($c['route_locator']);
        }));

        $container->set('route_collection', $container->factory(function(ContainerInterface $c) {
            $routes = new RouteCollection();
            $routes->addCollection($c['route_loader']->load('routes.php'));
        }));

        $container->set('controller_resolver', $container->factory(function(ContainerInterface $c) {
            return new ControllerResolver($c);
        }));

        $container->set('request_context', $container->factory(function(ContainerInterface $c) {
            $context = new RequestContext();
            $context->fromRequest($c['request']);
        }));

        $container->set('request_stack', new RequestStack());

        $container->set('request', function(ContainerInterface $c) {
            return $c->get('request_stack')->getCurrentRequest();
        });

        $container->set('url_matcher', $container->factory(function(ContainerInterface $c) {
            return new UrlMatcher($c['route_collection'], $c['request_context']);
        }));


        $container->set('Tomahawk\HttpKernel\HttpKernelInterface', $container->factory(function(ContainerInterface $c) {
            return new HttpKernel($c['event_dispatcher'], $c['url_Matcher'], $c['controller_resolver']);
        }));

        $container->set('session.storage.file', function(ContainerInterface $c) {
            $config = $c['config']->get('session');
            $nativeFileSessionHandler = new NativeFileSessionHandler($$config['save_path']);
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

            // @TODO Add session db config
            $pdo = null;

            $config = $c['config']->get('session');

            $pdoSessionHandler = new PdoSessionHandler($pdo, array(
                'db_table'    => 'tbl_session',
                'db_id_col'   => 'session_id',
                'db_data_col' => 'session_data',
                'db_time_col' => 'session_timestamp',
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

            $session = $c['config']->get('session.storage');

            return new Session($c['session.storage.' .$session]);
        });

    }

    protected function registerAliases(ContainerInterface $container)
    {
        $container->addAlias('asset_manager', 'Tomahawk\Asset\AssetManagerInterface');
        $container->addAlias('cache', 'Tomahawk\Cache\CacheInterface');
        $container->addAlias('database', 'Tomahawk\Database\DatabaseManager');
        $container->addAlias('encrypter', 'Tomahawk\Encryption\CryptInterface');
        $container->addAlias('event_dispatcher', 'Symfony\Component\EventDispatcherInterface');
        $container->addAlias('cookies', 'Tomahawk\HttpCore\Response\CookiesInterface');
        $container->addAlias('form_manager', 'Tomahawk\Forms\FormsManagerInterface');
        $container->addAlias('html_builder', 'Tomahawk\Html\HtmlBuilderInterface');
        $container->addAlias('http_kernel', 'Tomahawk\HttpKernel\HttpKernelInterface');
        $container->addAlias('response_builder', 'Tomahawk\HttpCore\ResponseBuilderInterface');
        // Request might not be needed....
        $container->addAlias('request', 'Symfony\Component\HttpFoundation\Request');
        $container->addAlias('session', 'Tomahawk\Session\SessionInterface');
        $container->addAlias('view_generator', 'Tomahawk\View\ViewGeneratorInterface');

    }

}
<?php

namespace Tomahawk\Bundle\FrameworkBundle\Resources\Services;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\PhpEngine;
use Tomahawk\Auth\Handlers\EloquentAuthHandler;
use Tomahawk\Cache\Provider\ApcProvider;
use Tomahawk\Cache\Provider\ArrayProvider;
use Tomahawk\Cache\Provider\FilesystemProvider;
use Tomahawk\Cache\Provider\MemcacheProvider;
use Tomahawk\Cache\Provider\RedisProvider;
use Tomahawk\Cache\Provider\XcacheProvider;
use Tomahawk\Config\Loader\PhpConfigLoader;
use Tomahawk\Config\Loader\YamlConfigLoader;
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
use Tomahawk\Templating\Loader\FilesystemLoader;
use Tomahawk\Templating\Loader\TemplateLocator;
use Tomahawk\Templating\TemplateNameParser;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Auth\Auth;
use Tomahawk\Hashing\Hasher;

class FrameworkProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $this->registerServices($container);
        $this->registerAliases($container);
    }

    protected function registerServices(ContainerInterface $container)
    {

        $container->set('Tomahawk\Auth\AuthHandlerInterface', function(ContainerInterface $c) {
            return new EloquentAuthHandler($c['hasher'], $c['config']->get('security.model'));
        });

        $container->set('Tomahawk\Auth\AuthInterface', function(ContainerInterface $c) {
            return new Auth($c['session'], $c['auth_handler']);
        });

        $container->set('illuminate_database', function(ContainerInterface $c) {

            /**
             * @var ConfigInterface
             */
            $config = $c['config'];

            $manager = new Manager();

            $connections = $config->get('database.connections');

            foreach ($connections as $name => $connection)
            {
                $manager->addConnection($connection, $name);
            }

            $manager->getDatabaseManager()->setDefaultConnection($config->get('database.default'));

            $manager->bootEloquent();
            $manager->setAsGlobal();

            return $manager;
        });

        $container->addAlias('event_dispatcher', 'Symfony\Component\EventDispatcherInterface');

        $container->set('Symfony\Component\EventDispatcherInterface', new EventDispatcher());

        $container->set('Tomahawk\Asset\AssetManagerInterface', function(ContainerInterface $container) {
            return new AssetManager($container['html_builder']);
        });

        $container->set('Symfony\Component\Config\Loader\LoaderInterface', function(ContainerInterface $c) {

            $kernel = $c['kernel'];
            $defaultPath = $kernel->getRootDir() .'/Resources/config';

            $locator = new \Symfony\Component\Config\FileLocator(array($defaultPath));

            $loaderResolver = new LoaderResolver(
                array(
                    new YamlConfigLoader($locator),
                    new PhpConfigLoader($locator)
                )
            );

            return new DelegatingLoader($loaderResolver);
        });

        $container->set('Tomahawk\Config\ConfigInterface', function(ContainerInterface $c) {

            $defaultPath = $c['kernel']->getRootDir() .'/config';

            $config = new ConfigManager($c['config_loader'], array($defaultPath));
            $config->load();

            return $config;
        });

        $container->set('Tomahawk\DI\ContainerInterface', function(ContainerInterface $c) {
            return $c;
        });

        $container->set('cache.providers.array', function() {
            return new ArrayProvider(new ArrayCache());
        });

        $container->set('cache.providers.filesystem', function(ContainerInterface $c) {
            $config = $c['config'];

            return new FilesystemProvider(new FilesystemCache($config->get('cache.directory')));
        });

        $container->set('cache.providers.apc', function(ContainerInterface $c) {
            $config = $c['config'];
            return new ApcProvider(new ApcCache());
        });

        $container->set('cache.providers.redis', function(ContainerInterface $c) {
            $config = $c['config'];

            $redisCache = new RedisCache();
            $redisCache->setRedis(new \Redis());
            return new RedisProvider($redisCache);
        });

        $container->set('cache.providers.memcache', function(ContainerInterface $c) {
            $memcache = new MemcacheCache();
            $memcache->setMemcache(new \Memcache());
            return new MemcacheProvider($memcache);
        });

        $container->set('cache.providers.xcache', function(ContainerInterface $c) {
            return new XcacheProvider(new XcacheCache());
        });

        $container->set('Tomahawk\Cache\CacheInterface', function(ContainerInterface $c) {

            $provider = $c['config']->get('cache.driver', 'array');
            return new CacheManager($c['cache.providers.' .$provider]);
        });

        $container->set('Tomahawk\Database\DatabaseManager', function(ContainerInterface $c) {
            return new DatabaseManager($c['illuminate_database']->getDatabaseManager());
        });

        $container->set('Tomahawk\Encryption\CryptInterface', function(ContainerInterface $c) {
            return new Crypt($c['config']->get('security.key'));
        });

        $container->set('Tomahawk\Forms\FormsManagerInterface', new FormsManager());

        $container->set('Tomahawk\Html\HtmlBuilderInterface', new HtmlBuilder());

        $container->set('Tomahawk\Hashing\HasherInterface', function(ContainerInterface $c) {
            return new Hasher();
        });

        $container->set('Tomahawk\HttpCore\ResponseBuilderInterface', new ResponseBuilder());

        $container->set('Tomahawk\HttpCore\Response\CookiesInterface', $container->factory(function(ContainerInterface $c) {
            return new Cookies($c['request'], array());
        }));

        $container->set('Symfony\Component\Templating\EngineInterface', $container->factory(function(ContainerInterface $c) {

            $kernel = $c->get('kernel');
            $locator = new FileLocator($kernel);
            $templateLocator = new TemplateLocator($locator);

            $loader = new FilesystemLoader($templateLocator);

            $parser = new TemplateNameParser($kernel);

            $phpEngine = new PhpEngine($parser, $loader);
            //$environment = new \Twig_Environment()
            //$twigEngine = new TwigEngine(, $parser);

            return new DelegatingEngine(array(
                $phpEngine
            ));

        }));

        $container->set('monolog_logger', function() {
            return null;
        });

        $container->set('route_listener', function(ContainerInterface $c) {
            return new RouterListener($c['url_matcher'], $c['request_context'], $c['monolog_logger'], $c['request_stack']);
        });

        $container->set('route_locator', $container->factory(function(ContainerInterface $c) {

            $kernel = $c['kernel'];

            $defaultPath = $kernel->getRootDir() .'/Resources';

            $locator = new FileLocator($kernel, $defaultPath);

            return $locator;
            //return new FileLocator(array(__DIR__ .'/../app/Resources/'));
        }));

        $container->set('route_loader', $container->factory(function(ContainerInterface $c) {
            return new PhpFileLoader($c['route_locator']);
        }));

        $container->set('route_collection', function(ContainerInterface $c) {
            $routes = new RouteCollection();
            $routes->addCollection($c['route_loader']->load('routes.php'));

            return $routes;
        });

        $container->set('controller_resolver', $container->factory(function(ContainerInterface $c) {
            return new ControllerResolver($c);
        }));

        $container->set('request_context', $container->factory(function(ContainerInterface $c) {
            $context = new RequestContext();
            $context->fromRequest($c['request']);

            return $context;
        }));

        $container->set('request_stack', new RequestStack());


        $container->set('Symfony\Component\HttpFoundation\Request', function(ContainerInterface $c) {
            return $c->get('request_stack')->getCurrentRequest() ?: Request::createFromGlobals();
        });

        $container->set('url_matcher', $container->factory(function(ContainerInterface $c) {
            return new UrlMatcher($c['route_collection'], $c['request_context']);
        }));


        $container->set('Tomahawk\HttpKernel\HttpKernelInterface', $container->factory(function(ContainerInterface $c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver'], $c['request_stack']);
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
            $pdo = $c['database']->connection()->getPdo();

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

            $session = $c['config']->get('session.storage','array');

            return new Session($c['session.storage.' .$session]);
        });

    }

    protected function registerAliases(ContainerInterface $container)
    {

        $container->addAlias('auth', 'Tomahawk\Auth\AuthInterface');
        $container->addAlias('auth_handler', 'Tomahawk\Auth\AuthHandlerInterface');
        $container->addAlias('asset_manager', 'Tomahawk\Asset\AssetManagerInterface');
        $container->addAlias('cache', 'Tomahawk\Cache\CacheInterface');
        $container->addAlias('config_loader', 'Symfony\Component\Config\Loader\LoaderInterface');
        $container->addAlias('config', 'Tomahawk\Config\ConfigInterface');
        $container->addAlias('hasher', 'Tomahawk\Hashing\HasherInterface');
        $container->addAlias('database', 'Tomahawk\Database\DatabaseManager');
        $container->addAlias('encrypter', 'Tomahawk\Encryption\CryptInterface');
        $container->addAlias('cookies', 'Tomahawk\HttpCore\Response\CookiesInterface');
        $container->addAlias('form_manager', 'Tomahawk\Forms\FormsManagerInterface');
        $container->addAlias('html_builder', 'Tomahawk\Html\HtmlBuilderInterface');
        $container->addAlias('http_kernel', 'Tomahawk\HttpKernel\HttpKernelInterface');
        $container->addAlias('response_builder', 'Tomahawk\HttpCore\ResponseBuilderInterface');
        // Request might not be needed....
        $container->addAlias('request', 'Symfony\Component\HttpFoundation\Request');
        $container->addAlias('session', 'Tomahawk\Session\SessionInterface');
        $container->addAlias('templating', 'Symfony\Component\Templating\EngineInterface');

    }

}
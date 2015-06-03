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

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader as TransPhpFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Tomahawk\Auth\Handlers\DatabaseAuthHandler;
use Tomahawk\Auth\Handlers\EloquentAuthHandler;
use Tomahawk\Bundle\FrameworkBundle\Events\LocaleListener;
use Tomahawk\Cache\Provider\ApcProvider;
use Tomahawk\Cache\Provider\ArrayProvider;
use Tomahawk\Cache\Provider\FilesystemProvider;
use Tomahawk\Cache\Provider\MemcachedProvider;
use Tomahawk\Cache\Provider\MemcacheProvider;
use Tomahawk\Cache\Provider\RedisProvider;
use Tomahawk\Cache\Provider\XcacheProvider;
use Tomahawk\CommandBus\CommandBus;
use Tomahawk\CommandBus\CommandHandlerResolver;
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
use Tomahawk\Input\InputManager;
use Tomahawk\Session\Session;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\Routing\Controller\ControllerResolver;
use Tomahawk\Routing\Controller;
use Tomahawk\Forms\FormsManager;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\Translation\Translator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Templating\Helper\BlocksHelper;
use Tomahawk\Templating\Helper\InputHelper;
use Tomahawk\Templating\Helper\RequestHelper;
use Tomahawk\Templating\Helper\SessionHelper;
use Tomahawk\Templating\Helper\TranslatorHelper;
use Tomahawk\Templating\Helper\UrlHelper;
use Tomahawk\Templating\Loader\FilesystemLoader;
use Tomahawk\Templating\Twig\Extension\RequestExtension;
use Tomahawk\Templating\Twig\Extension\SessionExtension;
use Tomahawk\Templating\Twig\Extension\TranslatorExtension;
use Tomahawk\Templating\Twig\Extension\UrlExtension;
use Tomahawk\Templating\Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Tomahawk\Templating\Loader\TemplateLocator;
use Tomahawk\Templating\TemplateNameParser;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Auth\Auth;
use Tomahawk\Hashing\Hasher;
use Tomahawk\Templating\Twig\TwigEngine;
use Tomahawk\Url\UrlGenerator;

class FrameworkProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $this->registerServices($container);
        $this->registerAliases($container);
    }

    protected function registerServices(ContainerInterface $container)
    {
        $container->set('auth_handler', function(ContainerInterface $c) {
            $handler = $c['config']->get('security.handler');
            return $c[$handler . '_auth_handler'];
        });

        $container->set('eloquent_auth_handler', function(ContainerInterface $c) {
            $eloquentConfig = $c['config']->get('security.handlers.eloquent');
            return new EloquentAuthHandler($c['hasher'], $eloquentConfig['model']);
        });

        $container->set('database_auth_handler', function(ContainerInterface $c) {
            /** @var ConfigInterface $config */
            $config = $c['config'];

            $databaseConfig = $config->get('security.handlers.database');

            $connection = $c['illuminate_database']->getDatabaseManager()->connection($databaseConfig['connection']);

            return new DatabaseAuthHandler(
                $c['hasher'],
                $connection,
                $databaseConfig['table'],
                $databaseConfig['key'],
                $databaseConfig['password']
            );
        });

        $container->set('Tomahawk\Auth\AuthInterface', function(ContainerInterface $c) {
            return new Auth($c['session'], $c['auth_handler']);
        });

        $container->set('Illuminate\Database\Capsule\Manager', function(ContainerInterface $c) {

            /** @var ConfigInterface $config */
            $config = $c['config'];

            $manager = new Manager();

            $connections = $config->get('database.connections');

            foreach ($connections as $name => $connection) {
                $manager->addConnection($connection, $name);
            }

            $manager->setFetchMode($config->get('database.fetch'));
            $manager->getDatabaseManager()->setDefaultConnection($config->get('database.default'));

            $manager->bootEloquent();
            $manager->setAsGlobal();

            return $manager;
        });

        $container->set('Symfony\Component\EventDispatcherInterface', new EventDispatcher());

        $container->set('Tomahawk\Asset\AssetManagerInterface', function(ContainerInterface $c) {
            return new AssetManager($c['html_builder'], $c['url_generator']);
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

            $kernel = $c['kernel'];

            $paths = array(
                $kernel->getRootDir() .'/config',
            );

            // Check if we have an environment config
            if (file_exists($kernel->getRootDir() .'/config/' . $kernel->getEnvironment())) {
                $paths[] = $kernel->getRootDir() .'/config/' . $kernel->getEnvironment();
            }

            $config = new ConfigManager($c['config_loader'], $paths);
            $config->load();

            return $config;
        });

        $container->set('Tomahawk\DI\ContainerInterface', function(ContainerInterface $c) {
            return $c;
        });

        $container->set('cache.providers.apc', function(ContainerInterface $c) {
            $cache = new ApcCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new ApcProvider($cache);
        });

        $container->set('cache.providers.array', function(ContainerInterface $c) {
            $cache = new ArrayCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new ArrayProvider($cache);
        });

        $container->set('cache.providers.filesystem', function(ContainerInterface $c) {
            $cache = new FilesystemCache($c['config']->get('cache.directory'));
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new FilesystemProvider($cache);
        });

        $container->set('cache.providers.memcached', function(ContainerInterface $c) {
            $cache = new MemcachedCache();
            $cache->setMemcached(new \Memcached());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new MemcachedProvider($cache);
        });

        $container->set('cache.providers.memcache', function(ContainerInterface $c) {
            $cache = new MemcacheCache();
            $cache->setMemcache(new \Memcache());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new MemcacheProvider($cache);
        });

        $container->set('cache.providers.redis', function(ContainerInterface $c) {
            $cache = new RedisCache();
            $cache->setRedis(new \Redis());
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new RedisProvider($cache);
        });

        $container->set('cache.providers.xcache', function(ContainerInterface $c) {
            $cache = new XcacheCache();
            $cache->setNamespace($c['config']->get('cache.namespace', ''));
            return new XcacheProvider($cache);
        });

        $container->set('Tomahawk\Cache\CacheInterface', function(ContainerInterface $c) {
            $provider = $c['config']->get('cache.driver', 'array');
            if (!$c->has('cache.providers.' .$provider)) {
                throw new \Exception(sprintf('Cache provider %s does not exist or has not been set.', $provider));
            }
            return new CacheManager($c['cache.providers.' .$provider]);
        });

        $container->set('filesystem', function() {
            return new Filesystem();
        });

        $container->set('Tomahawk\Database\DatabaseManager', function(ContainerInterface $c) {
            return new DatabaseManager($c['illuminate_database']->getDatabaseManager());
        });

        $container->set('Tomahawk\Encryption\CryptInterface', function(ContainerInterface $c) {
            return new Crypt($c['config']->get('security.key'));
        });

        $container->set('Tomahawk\Forms\FormsManagerInterface', new FormsManager());

        $container->set('Tomahawk\Input\InputInterface', function(ContainerInterface $c) {
            return new InputManager($c['request'], $c['session']);
        });

        $container->set('Tomahawk\Html\HtmlBuilderInterface', new HtmlBuilder());

        $container->set('Tomahawk\Hashing\HasherInterface', function(ContainerInterface $c) {
            return new Hasher();
        });

        $container->set('Tomahawk\HttpCore\ResponseBuilderInterface', new ResponseBuilder());

        $container->set('Tomahawk\HttpCore\Response\CookiesInterface', $container->factory(function(ContainerInterface $c) {
            return new Cookies($c['request'], array());
        }));

        $container->set('templating.php.helpers', function(ContainerInterface $c) {

            return array(
                new SlotsHelper(),
                new BlocksHelper(),
                new TranslatorHelper($c['translator']),
                new UrlHelper($c['url_generator']),
                new RequestHelper($c['request_stack']),
                new SessionHelper($c['session']),
                new InputHelper($c['input']),
            );
        });

        $container->set('templating.twig.extensions', function(ContainerInterface $c) {

            return array(
                new TranslatorExtension($c['translator']),
                new UrlExtension($c['url_generator']),
                new RequestExtension($c['request_stack']),
                new SessionExtension($c['session']),
            );
        });

        $container->set('templating.engine.php', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $loader = new FilesystemLoader($templateLocator);
            $parser = new TemplateNameParser($kernel);
            return new PhpEngine($parser, $loader, $c['templating.php.helpers']);
        });

        $container->set('templating.engine.twig', function(ContainerInterface $c) {
            $kernel = $c['kernel'];
            $locator = new FileLocator($kernel, $kernel->getRootDir() . '/Resources/');
            $templateLocator = new TemplateLocator($locator);
            $parser = new TemplateNameParser($kernel);

            $loader = new TwigFilesystemLoader($templateLocator, $parser);

            $twig = new \Twig_Environment($loader);

            $extensions = $c['templating.twig.extensions'];

            foreach ($extensions as $extension) {
                $twig->addExtension($extension);
            }

            return new TwigEngine($twig, $parser, $locator);
        });

        $container->tag('templating.engine.php', 'templating.engine');
        $container->tag('templating.engine.twig', 'templating.engine');

        $container->set('Symfony\Component\Templating\EngineInterface', $container->factory(function(ContainerInterface $c) {

            $engineServiceIds = $c->findTaggedServiceIds('templating.engine');

            $engines = array();

            foreach ($engineServiceIds as $engineServiceId) {
                $engines[] = $c[$engineServiceId];
            }

            return new DelegatingEngine($engines);
        }));

        $container->set('route_logger', null);

        $container->set('Psr\Log\LoggerInterface', function(ContainerInterface $c) {

            $config = $c['config'];
            $kernel = $c['kernel'];
            $defaultLogName = 'tomahawk.log';
            $defaultLogPath = $kernel->getRootDir() .'/app/storage/logs/';

            $logPath = $config->get('monolog.path', $defaultLogPath);
            $logName = $config->get('monolog.name', $defaultLogName);;

            $stream = $logPath . $logName;

            $formatter = new LineFormatter(null, null, true, true);

            $handler = new RotatingFileHandler($stream, 0, Logger::WARNING);
            $handler->setFormatter($formatter);

            // Create a log channel
            $log = new Logger('tomahawk_logger');
            $log->pushHandler($handler);

            return $log;
        });

        $container->set('route_listener', function(ContainerInterface $c) {
            return new RouterListener($c['url_matcher'], $c['request_context'], $c['route_logger'], $c['request_stack']);
        });

        $container->set('route_locator', $container->factory(function(ContainerInterface $c) {

            $kernel = $c['kernel'];

            $defaultPath = $kernel->getRootDir() .'/Resources';

            $locator = new FileLocator($kernel, $defaultPath);

            return $locator;
        }));

        $container->set('route_loader', $container->factory(function(ContainerInterface $c) {
            return new PhpFileLoader($c['route_locator']);
        }));

        $container->set('route_collection', function(ContainerInterface $c) {

            $kernel = $c['kernel'];
            $bundleRoutePaths = $kernel->getRoutePaths();

            $routes = new RouteCollection();
            $routes->addCollection($c['route_loader']->load('routes.php'));

            foreach ($bundleRoutePaths as $bundleRoutePath) {
                $routes->addCollection($c['route_loader']->load($bundleRoutePath));
            }

            return $routes;
        });

        $container->set('controller_resolver', $container->factory(function(ContainerInterface $c) {
            return new ControllerResolver($c);
        }));

        $container->set('request_context', function(ContainerInterface $c) {
            $config = $c['config'];

            return new RequestContext(
                $config->get('request.base_url', ''),
                'GET',
                $config->get('request.host', 'localhost'),
                $config->get('request.scheme', 'http'),
                $config->get('request.http_port', 80),
                $config->get('request.https_port', 443)
            );
        });

        $container->set('locale_listener', function(ContainerInterface $c) {
            $config = $c['config'];
            $locale = $config->get('translation.locale');

            return new LocaleListener($locale, $c['request_stack'], $c['request_context']);
        });

        $container->set('Symfony\Component\HttpFoundation\RequestStack', new RequestStack());

        $container->set('Symfony\Component\HttpFoundation\Request', function(ContainerInterface $c) {
            return $c['request_stack']->getCurrentRequest() ?: Request::createFromGlobals();
        });

        $container->set('url_matcher', $container->factory(function(ContainerInterface $c) {
            return new UrlMatcher($c['route_collection'], $c['request_context']);
        }));

        $container->set('Tomahawk\Url\UrlGeneratorInterface', function(ContainerInterface $c) {
            $urlGenerator  = new UrlGenerator($c['route_collection'], $c['request_context']);
            $urlGenerator->setSslOn($c['config']->get('request.ssl', true));
            return $urlGenerator;
        });

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

            $pdo = $c['database']->connection()->getPdo();

            $config = $c['config']->get('session');

            $pdoSessionHandler = new PdoSessionHandler($pdo, array(
                'db_table'    => $config['tomahawk_sessions'],
                'db_id_col'   => $config['id'],
                'db_data_col' => $config['data'],
                'db_time_col' => $config['date'],
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

        $container->set('Symfony\Component\Translation\TranslatorInterface', function(ContainerInterface $c) {

            $config = $c['config'];
            $locale = $config->get('translation.locale');
            $fallbackLocale = $config->get('translation.fallback_locale');
            $translationDirs = $config->get('translation.translation_dirs');
            $cacheDir = $config->get('translation.cache_dir');

            $translator = new Translator($locale, new MessageSelector(), $cacheDir);
            $translator->setFallbackLocales(array($fallbackLocale));
            $translator->addLoader('php', new TransPhpFileLoader());
            $translator->addLoader('array', new ArrayLoader());

            foreach ($translationDirs as $translationDir) {

                $finder = new Finder();

                $finder->in($translationDir)->depth(0)->directories();

                foreach ($finder as $directory)  {

                    $dFinder = new Finder();
                    $dFinder->in($directory->getPathname())->files()->name('*.php');

                    foreach ($dFinder as $file) {
                        $translator->addResource('php', $file->getPathname(), $directory->getFileName());
                    }
                }
            }

            return $translator;
        });

        $container->set('Tomahawk\CommandBus\CommandHandlerResolverInterface', function(ContainerInterface $c) {
            return new CommandHandlerResolver($c);
        });

        $container->set('Tomahawk\CommandBus\CommandBusInterface', function(ContainerInterface $c) {
            return new CommandBus($c['commandbus_handler_resolver']);
        });
    }

    protected function registerAliases(ContainerInterface $container)
    {
        $container->addAlias('auth', 'Tomahawk\Auth\AuthInterface');
        $container->addAlias('asset_manager', 'Tomahawk\Asset\AssetManagerInterface');
        $container->addAlias('cache', 'Tomahawk\Cache\CacheInterface');
        $container->addAlias('commandbus', 'Tomahawk\CommandBus\CommandBusInterface');
        $container->addAlias('commandbus_handler_resolver', 'Tomahawk\CommandBus\CommandHandlerResolverInterface');
        $container->addAlias('config_loader', 'Symfony\Component\Config\Loader\LoaderInterface');
        $container->addAlias('config', 'Tomahawk\Config\ConfigInterface');
        $container->addAlias('hasher', 'Tomahawk\Hashing\HasherInterface');
        $container->addAlias('database', 'Tomahawk\Database\DatabaseManager');
        $container->addAlias('encrypter', 'Tomahawk\Encryption\CryptInterface');
        $container->addAlias('event_dispatcher', 'Symfony\Component\EventDispatcherInterface');
        $container->addAlias('cookies', 'Tomahawk\HttpCore\Response\CookiesInterface');
        $container->addAlias('form_manager', 'Tomahawk\Forms\FormsManagerInterface');
        $container->addAlias('html_builder', 'Tomahawk\Html\HtmlBuilderInterface');
        $container->addAlias('http_kernel', 'Tomahawk\HttpKernel\HttpKernelInterface');
        $container->addAlias('illuminate_database', 'Illuminate\Database\Capsule\Manager');
        $container->addAlias('monolog_logger', 'Psr\Log\LoggerInterface');
        $container->addAlias('logger', 'Psr\Log\LoggerInterface');
        $container->addAlias('response_builder', 'Tomahawk\HttpCore\ResponseBuilderInterface');
        // Request might not be needed....
        $container->addAlias('request', 'Symfony\Component\HttpFoundation\Request');
        $container->addAlias('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');
        $container->addAlias('session', 'Tomahawk\Session\SessionInterface');
        $container->addAlias('input', 'Tomahawk\Input\InputInterface');
        $container->addAlias('templating', 'Symfony\Component\Templating\EngineInterface');
        $container->addAlias('translator', 'Symfony\Component\Translation\TranslatorInterface');
        $container->addAlias('url_generator', 'Tomahawk\Url\UrlGeneratorInterface');
    }
}

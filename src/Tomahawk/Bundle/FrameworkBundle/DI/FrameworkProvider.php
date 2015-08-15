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
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\Bundle\FrameworkBundle\Events\LocaleListener;
use Tomahawk\DI\ServiceProviderInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpCore\ResponseBuilder;
use Tomahawk\Encryption\Crypt;
use Tomahawk\Database\DatabaseManager;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Input\InputManager;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\Routing\Controller;
use Tomahawk\Forms\FormsManager;
use Tomahawk\HttpCore\Response\Cookies;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Hashing\Hasher;
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

        $container->set('Symfony\Component\EventDispatcher\EventDispatcherInterface', new EventDispatcher());

        $container->set('Tomahawk\Asset\AssetManagerInterface', function(ContainerInterface $c) {
            return new AssetManager($c['html_builder'], $c['url_generator']);
        });

        $container->set('Tomahawk\DI\ContainerInterface', function(ContainerInterface $c) {
            return $c;
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

        $container->set('Psr\Log\LoggerInterface', function(ContainerInterface $c) {

            $config = $c['config'];
            $kernel = $c['kernel'];
            $defaultLogName = 'tomahawk.log';
            $defaultLogPath = $kernel->getRootDir() .'/app/storage/logs/';

            // We do this check without adding a default to the config call so we can test it
            $logPath = $config->get('monolog.path');
            $logName = $config->get('monolog.name');

            $stream = $logPath . $logName;

            // Check if we have a stream from the config above
            // if not use default
            if ( ! $stream) {
                $stream =  $defaultLogPath . $defaultLogName;
            }

            $formatter = new LineFormatter(null, null, true, true);

            $handler = new RotatingFileHandler($stream, 0, Logger::WARNING);
            $handler->setFormatter($formatter);

            // Create a log channel
            $log = new Logger('tomahawk_logger');
            $log->pushHandler($handler);

            return $log;
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

        $container->set('Tomahawk\Url\UrlGeneratorInterface', function(ContainerInterface $c) {
            $urlGenerator  = new UrlGenerator($c['route_collection'], $c['request_context']);
            $urlGenerator->setSslOn($c['config']->get('request.ssl', true));
            return $urlGenerator;
        });

        $container->set('Tomahawk\HttpKernel\HttpKernelInterface', $container->factory(function(ContainerInterface $c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver'], $c['request_stack']);
        }));

    }

    protected function registerAliases(ContainerInterface $container)
    {
        $container->addAlias('asset_manager', 'Tomahawk\Asset\AssetManagerInterface');
        $container->addAlias('hasher', 'Tomahawk\Hashing\HasherInterface');
        $container->addAlias('database', 'Tomahawk\Database\DatabaseManager');
        $container->addAlias('encrypter', 'Tomahawk\Encryption\CryptInterface');
        $container->addAlias('event_dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $container->addAlias('cookies', 'Tomahawk\HttpCore\Response\CookiesInterface');
        $container->addAlias('form_manager', 'Tomahawk\Forms\FormsManagerInterface');
        $container->addAlias('html_builder', 'Tomahawk\Html\HtmlBuilderInterface');
        $container->addAlias('http_kernel', 'Tomahawk\HttpKernel\HttpKernelInterface');
        $container->addAlias('input', 'Tomahawk\Input\InputInterface');
        $container->addAlias('illuminate_database', 'Illuminate\Database\Capsule\Manager');
        $container->addAlias('monolog_logger', 'Psr\Log\LoggerInterface');
        $container->addAlias('logger', 'Psr\Log\LoggerInterface');
        $container->addAlias('response_builder', 'Tomahawk\HttpCore\ResponseBuilderInterface');
        // Request might not be needed....
        $container->addAlias('request', 'Symfony\Component\HttpFoundation\Request');
        $container->addAlias('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');
        $container->addAlias('input', 'Tomahawk\Input\InputInterface');
        $container->addAlias('url_generator', 'Tomahawk\Url\UrlGeneratorInterface');
    }
}

<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\DependencyInjection;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\Bundle\FrameworkBundle\EventListener\LocaleListener;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpCore\ResponseBuilder;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\Input\InputManager;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\Forms\FormsManager;
use Tomahawk\HttpCore\Response\Cookies;
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
        $container->set('Symfony\Component\EventDispatcher\EventDispatcherInterface', new EventDispatcher());

        $container->set('Tomahawk\Asset\AssetManagerInterface', function(ContainerInterface $c) {
            return new AssetManager($c['html_builder'], $c['url_generator']);
        });

        $container->set('Tomahawk\DependencyInjection\ContainerInterface', function(ContainerInterface $c) {
            return $c;
        });

        $container->set('filesystem', function() {
            return new Filesystem();
        });

        $container->set('Tomahawk\Forms\FormsManagerInterface', new FormsManager());

        $container->set('Tomahawk\Input\InputInterface', function(ContainerInterface $c) {
            return new InputManager($c['request_stack']->getCurrentRequest(), $c['session']);
        });

        $container->set('Tomahawk\Html\HtmlBuilderInterface', new HtmlBuilder());

        $container->set('Tomahawk\Hashing\HasherInterface', function(ContainerInterface $c) {
            return new Hasher();
        });

        $container->set('Tomahawk\HttpCore\ResponseBuilderInterface', new ResponseBuilder());

        $container->set('Tomahawk\HttpCore\Response\CookiesInterface', function(ContainerInterface $c) {
            return new Cookies($c['request_stack']->getCurrentRequest());
        });

        $container->set('locale_listener', function(ContainerInterface $c) {
            $config = $c['config'];
            $locale = $config->get('translation.locale');

            return new LocaleListener($locale, $c['request_stack'], $c['request_context']);
        });

        $container->set('Symfony\Component\HttpFoundation\RequestStack', new RequestStack());

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
        $container->addAlias('response_builder', 'Tomahawk\HttpCore\ResponseBuilderInterface');
        $container->addAlias('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');
        $container->addAlias('input', 'Tomahawk\Input\InputInterface');
        $container->addAlias('url_generator', 'Tomahawk\Url\UrlGeneratorInterface');
    }
}

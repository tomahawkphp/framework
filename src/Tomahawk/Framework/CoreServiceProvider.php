<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Framework;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\BootableProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\EventsProviderInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Filesystem\Filesystem;
use Tomahawk\Forms\FormsManager;
use Tomahawk\Framework\EventListener\CookieListener;
use Tomahawk\Framework\EventListener\LocaleListener;
use Tomahawk\Framework\EventListener\StringResponseListener;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\HttpCore\Request;
use Tomahawk\HttpCore\Response\Cookies;
use Tomahawk\HttpKernel\HttpKernel;

/**
 * Class CoreServiceProvider
 *
 * @package Tomahawk\Framework
 */
class CoreServiceProvider implements BootableProviderInterface, ServiceProviderInterface, EventsProviderInterface
{

    /**
     * @param ContainerInterface $container An Container instance
     */
    public function boot(ContainerInterface $container)
    {
        // TODO: Implement boot() method.
    }

    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     * @return
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber($container->get('locale_listener'));
        $eventDispatcher->addSubscriber(new CookieListener($container));
        $eventDispatcher->addSubscriber(new StringResponseListener());
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param ContainerInterface $container An Container instance
     */
    public function register(ContainerInterface $container)
    {
        $container->set('Tomahawk\HttpCore\Response\CookiesInterface', function(ContainerInterface $c) {
            return new Cookies($c['request_stack']->getCurrentRequest());
        });

        $container->addAlias('cookies', 'Tomahawk\HttpCore\Response\CookiesInterface');


        $container->set('Symfony\Component\EventDispatcher\EventDispatcherInterface', new EventDispatcher());
        $container->addAlias('event_dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $container->set('Tomahawk\DependencyInjection\ContainerInterface', function(ContainerInterface $c) {
            return $c;
        });

        $container->set('locale_listener', function(ContainerInterface $c) {
            $config = $c->get('config');
            $locale = $config->get('translation.locale');

            return new LocaleListener($locale, $c['request_stack'], $c['request_context']);
        });

        $container->set('Tomahawk\Forms\FormsManagerInterface', new FormsManager());
        $container->addAlias('form_manager', 'Tomahawk\Forms\FormsManagerInterface');


        $container->set('Tomahawk\Html\HtmlBuilderInterface', new HtmlBuilder());
        $container->addAlias('html_builder', 'Tomahawk\Html\HtmlBuilderInterface');

        $container->set('filesystem', function() {
            return new Filesystem();
        });

        $container->set('Tomahawk\HttpKernel\HttpKernelInterface', $container->factory(function(ContainerInterface $c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver'], $c['request_stack'], $c['argument_resolver']);
        }));

        $container->addAlias('http_kernel', 'Tomahawk\HttpKernel\HttpKernelInterface');

        $container->set('Symfony\Component\HttpFoundation\RequestStack', new RequestStack());
        $container->addAlias('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');

    }
}

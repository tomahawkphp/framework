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

use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\Config\FileLocator;
use Tomahawk\Routing\Controller\ControllerNameParser;
use Tomahawk\Routing\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Tomahawk\Routing\Matcher\RedirectableUrlMatcher;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container)
    {
        $container->set('route_listener', function(ContainerInterface $c) {
            return new RouterListener($c['redirect_url_matcher'], $c['request_stack'], $c['request_context'], $c['route_logger']);
        });

        $container->set('route_locator', $container->factory(function(ContainerInterface $c) {

            $kernel = $c['kernel'];

            $defaultPath = $kernel->getProjectDir() .'/config';

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

        $container->set('argument_resolver', function() {
            return new ArgumentResolver();
        });

        $container->set('controller_resolver', $container->factory(function(ContainerInterface $c) {
            return new ControllerResolver($c, null, new ControllerNameParser($c['kernel']));
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

        $container->set('url_matcher', $container->factory(function(ContainerInterface $c) {
            return new UrlMatcher($c['route_collection'], $c['request_context']);
        }));

        $container->set('redirect_url_matcher', $container->factory(function(ContainerInterface $c) {
            return new RedirectableUrlMatcher($c['route_collection'], $c['request_context']);
        }));

        $container->set('route_logger', null);
    }
}

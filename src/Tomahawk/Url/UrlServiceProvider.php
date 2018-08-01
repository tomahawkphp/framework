<?php

namespace Tomahawk\Url;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

class UrlServiceProvider implements ServiceProviderInterface
{

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
        $container->set(UrlGeneratorInterface::class, function(ContainerInterface $c) {
            $urlGenerator = new UrlGenerator($c['route_collection'], $c['request_context']);
            $urlGenerator->setSslOn($c['config']->get('request.ssl', true));
            return $urlGenerator;
        });

        $container->addAlias('url_generator', UrlGeneratorInterface::class);
    }
}

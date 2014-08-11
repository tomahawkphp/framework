<?php

namespace Tomahawk\Bundle\FrameworkBundle;

use Symfony\Component\HttpFoundation\Response;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Bundle\FrameworkBundle\Resources\Services\FrameworkProvider;
use Tomahawk\Config\ConfigInterface;

class FrameworkBundle extends Bundle
{

    public function boot()
    {
        $this->container->registerProvider(new FrameworkProvider());

        $c = $this->container;

        $eventDispatcher = $c->get('event_dispatcher');

        $eventDispatcher->addSubscriber($c->get('route_listener'));

        if ($trustedProxies = $this->getConfig()->get('kernel.trusted_proxies')) {
            Request::setTrustedProxies($trustedProxies);
        }

        if ($this->getConfig()->get('kernel.http_method_override')) {
            Request::enableHttpMethodParameterOverride();
        }

        if ($trustedHosts = $this->getConfig()->get('kernel.trusted_hosts')) {
            Request::setTrustedHosts($trustedHosts);
        }
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

}
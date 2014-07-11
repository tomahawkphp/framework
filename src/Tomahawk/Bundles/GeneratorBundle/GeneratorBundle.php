<?php

namespace Tomahawk\Bundles\GeneratorBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\HttpFoundation\Request;

class GeneratorBundle extends Bundle
{

    public function boot()
    {
        /*if ($trustedProxies = $this->container->getParameter('kernel.trusted_proxies')) {
            Request::setTrustedProxies($trustedProxies);
        }

        if ($this->container->getParameter('kernel.http_method_override')) {
            Request::enableHttpMethodParameterOverride();
        }

        if ($trustedHosts = $this->container->getParameter('kernel.trusted_hosts')) {
            Request::setTrustedHosts($trustedHosts);
        }*/
    }

    public function shutdown()
    {

    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

}
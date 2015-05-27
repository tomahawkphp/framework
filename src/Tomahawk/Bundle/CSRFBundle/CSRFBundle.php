<?php

namespace Tomahawk\Bundle\CSRFBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider;
use Tomahawk\Bundle\CSRFBundle\Event\TokenSubscriber;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;

class CSRFBundle extends Bundle implements ContainerAwareInterface
{
    public function boot()
    {
        $this->container->register(new CSRFProvider());
    }

    public function registerEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new TokenSubscriber($this->container->get('security.csrf.tokenmanager')));
    }
}

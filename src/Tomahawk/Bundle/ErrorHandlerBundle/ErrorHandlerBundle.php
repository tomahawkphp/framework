<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\ErrorHandlerBundle;

use Tomahawk\Bundle\ErrorHandlerBundle\Controller\ExceptionController;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\DebugHandlersListener;

class ErrorHandlerBundle extends Bundle
{
    public function boot()
    {
        $this->container->set('exception_listener', function(ContainerInterface $c) {
            return new ExceptionListener(
                'exception_controller:showAction',
                $this->container->get('logger')
            );
        });

        $this->container->set('exception_controller', function(ContainerInterface $c) {
            return new ExceptionController($c['twig'], $c['kernel']->isDebug());
        });

        $this->container->set('debug_handlers_listener', function(ContainerInterface $c) {
            return new DebugHandlersListener(null, $c['logger']);
        });
    }

    /**
     * Register any events for the bundle
     *
     * This is called after all bundles have been boot so you get access
     * to all the services
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($this->container->get('exception_listener'));
        $dispatcher->addSubscriber($this->container->get('debug_handlers_listener'));
    }
}

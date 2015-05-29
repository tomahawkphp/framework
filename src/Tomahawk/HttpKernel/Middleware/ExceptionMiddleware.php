<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\HttpKernel\Event\ExceptionListener;
use Tomahawk\HttpKernel\KernelInterface;
use Tomahawk\Middleware\Middleware;

class ExceptionMiddleware extends Middleware
{
    public function boot()
    {
        $this->getEventDispatcher()->addSubscriber(new ExceptionListener(
            $this->getTemplating(),
            $this->getLogger()
        ));
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return EngineInterface
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->container->get('kernel');
    }
}

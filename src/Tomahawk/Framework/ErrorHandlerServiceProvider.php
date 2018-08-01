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

use Symfony\Component\Debug\ErrorHandler;
use Tomahawk\DependencyInjection\BootableProviderInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class ErrorHandlerServiceProvider
 *
 * @package Tomahawk\Framework
 */
class ErrorHandlerServiceProvider implements BootableProviderInterface, ServiceProviderInterface
{

    /**
     * @param ContainerInterface $container An Container instance
     */
    public function boot(ContainerInterface $container)
    {
        ErrorHandler::register(null, false)->throwAt(0, true);
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
        // TODO: Implement register() method.
    }
}

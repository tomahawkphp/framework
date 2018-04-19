<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\DependencyInjection;

/**
 * Interface BootableProviderInterface
 * @package Tomahawk\DependencyInjection
 */
interface BootableProviderInterface
{
    /**
     * @param ContainerInterface $container An Container instance
     */
    public function boot(ContainerInterface $container);
}

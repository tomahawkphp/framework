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
 * Interface RoutesProviderInterface
 * @package Tomahawk\DependencyInjection
 */
interface RoutesProviderInterface
{
    /**
     * @return string Path to routes file
     */
    public function routes();
}

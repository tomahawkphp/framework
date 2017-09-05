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
 * Class ContainerAware
 * @package Tomahawk\DependencyInjection
 */
abstract class ContainerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}

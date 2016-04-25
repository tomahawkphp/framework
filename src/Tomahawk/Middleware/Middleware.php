<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Middleware;

use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

abstract class Middleware implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Boot the middleware
     *
     * @return mixed
     */
    abstract public function boot();


    /**
     * Returns the middleware name (the class short name).
     *
     * @return string The Middleware name
     *
     * @api
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name : substr($name, $pos + 1);
    }
}

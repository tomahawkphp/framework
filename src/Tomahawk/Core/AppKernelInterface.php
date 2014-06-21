<?php

namespace Tomahawk\Core;

use Symfony\Component\HttpKernel\HttpKernelInterface;


interface AppKernelInterface extends HttpKernelInterface
{
    /**
     * @param \Tomahawk\Core\Container $container
     */
    public function setContainer($container);

    /**
     * @return \Tomahawk\Core\Container
     */
    public function getContainer();
}
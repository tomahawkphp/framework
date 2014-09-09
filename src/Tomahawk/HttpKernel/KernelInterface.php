<?php

namespace Tomahawk\HttpKernel;

use Tomahawk\Middleware\Middleware;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\DI\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface KernelInterface extends HttpKernelInterface, \Serializable
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    public function registerBundles();

    /**
     * Returns an array of middleware to register
     *
     * @return Middleware[] An array of middleware instances
     */
    public function registerMiddleware();

    /**
     * Boots the current kernel.
     *
     * @api
     */
    public function boot();

    /**
     * Shutdowns the kernel.
     *
     * This method is mainly useful when doing functional testing.
     *
     * @api
     */
    public function shutdown();

    /**
     * Gets the registered bundle instances.
     *
     * @return BundleInterface[] An array of registered bundle instances
     *
     * @api
     */
    public function getBundles();

    /**
     * Returns a bundle and optionally its descendants by its name.
     *
     * @param string  $name  Bundle name
     * @param bool    $first Whether to return the first bundle only or together with its descendants
     *
     * @return BundleInterface|BundleInterface[] A BundleInterface instance or an array of BundleInterface instances if $first is false
     *
     * @throws \InvalidArgumentException when the bundle is not enabled
     *
     * @api
     */
    public function getBundle($name, $first = true);

    /**
     * Gets the name of the kernel.
     *
     * @return string The kernel name
     *
     * @api
     */
    public function getName();

    /**
     * Gets the environment.
     *
     * @return string The current environment
     *
     * @api
     */
    public function getEnvironment();

    /**
     * Checks if debug mode is enabled.
     *
     * @return bool    true if debug mode is enabled, false otherwise
     *
     * @api
     */
    public function isDebug();

    /**
     * Gets the application root dir.
     *
     * @return string The application root dir
     *
     * @api
     */
    public function getRootDir();

    /**
     * Gets the current container.
     *
     * @return ContainerInterface A ContainerInterface instance
     *
     * @api
     */
    public function getContainer();

    /**
     * Gets the request start time (not available if debug is disabled).
     *
     * @return int     The request start timestamp
     *
     * @api
     */
    public function getStartTime();

    /**
     * Gets the cache directory.
     *
     * @return string The cache directory
     *
     * @api
     */
    public function getCacheDir();

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     *
     * @api
     */
    public function getLogDir();

    /**
     * Gets the charset of the application.
     *
     * @return string The charset
     *
     * @api
     */
    public function getCharset();

    public function locateResource($name, $dir = null, $first = true);
}

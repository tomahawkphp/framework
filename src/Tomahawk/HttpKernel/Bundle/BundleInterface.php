<?php

namespace Tomahawk\HttpKernel\Bundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Console\Application;

interface BundleInterface
{

    /**
     * Boots the Bundle.
     */
    public function boot();

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown();

    /**
     * Gets the Bundle namespace.
     *
     * @return string The Bundle namespace
     *
     * @api
     */
    public function getNamespace();

    /**
     * Gets the Bundle directory path.
     *
     * @return string The Bundle absolute path
     *
     * @api
     */
    public function getPath();

    /**
     * Returns the bundle name (the class short name).
     *
     * @return string The Bundle name
     *
     * @api
     */
    public function getName();

    /**
     * Finds and registers Commands.
     *
     * Override this method if your bundle commands do not follow the conventions:
     *
     * * Commands are in the 'Command' sub-directory
     * * Commands extend Symfony\Component\Console\Command\Command
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application);

    /**
     * File path to load routes from
     *
     * /dir/to/routes.php
     *
     * @return mixed
     */
    public function getRoutePath();

    /**
     * Register any events for the bundle
     *
     * This is called after all bundles have been boot so you get access
     * to all the services
     *
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerEvents(EventDispatcherInterface $dispatcher);
}

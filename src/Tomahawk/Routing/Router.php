<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing;

use Symfony\Component\Routing\RouteCollection;

class Router
{
    /**
     * Whether we're in a route group
     *
     * @var bool
     */
    protected $inGroup = false;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes = null)
    {
        $this->routes = $routes ?: new RouteCollection();
    }

    /**
     * Set route collection
     *
     * @param RouteCollection $routes
     * @return $this
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * Get route collection
     *
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * GET Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function get($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('GET', $path, $name, $callback, $schemes);
    }

    /**
     * POST Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function post($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('POST', $path, $name, $callback, $schemes);
    }

    /**
     * PUT Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function put($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('PUT', $path, $name, $callback, $schemes);
    }

    /**
     * PATCH Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function patch($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('PATCH', $path, $name, $callback, $schemes);
    }

    /**
     * OPTIONS Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function options($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('OPTIONS', $path, $name, $callback, $schemes);
    }

    /**
     * DELETE Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function delete($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute('DELETE', $path, $name, $callback, $schemes);
    }

    /**
     * ANY Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function any($path, $name, $callback = null, array $schemes = [])
    {
        return $this->createRoute(array(), $path, $name, $callback, $schemes);
    }

    /**
     * Match a given path
     *
     * You would proceed this method call by setting allowed methods
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @param array $schemes
     * @return Route
     */
    public function match($path, $name, $callback = null, array $schemes = [])
    {
        return $this->any($path, $name, $callback, $schemes);
    }

    /**
     * @param $method
     * @param $path
     * @param $name
     * @param $callback
     * @param array $schemes
     * @return Route
     */
    public function createRoute($method, $path, $name, $callback, array $schemes = [])
    {
        $methods = is_array($method) ? $method : explode('|', $method);

        if ('/' !== $path) {
            $path = $this->formatPath($path); //Symfony always stores with a starting slash
        }

        // Create a new Route class
        $route = new Route($path, array('_controller' => $callback));
        $route->setSchemes($schemes);
        $route->setMethods($methods);

        // Add route to collection
        $this->routes->add($name, $route);

        return $route;
    }

    /**
     * Format Request Path
     *
     * @param $path
     * @return string
     */
    public function formatPath($path)
    {
        $path = '/' .trim(trim($path), '/');
        return $path;
    }

    /**
     * Create a route group
     *
     * @param string|array $options
     * @param \Closure $callback
     * @return $this
     */
    public function group($options, \Closure $callback)
    {
        $prefix = null;

        if ( ! (is_string($options) || is_array($options))) {
            throw new \RuntimeException('Options must either be a string or array');
        }

        if (is_string($options)) {
            $prefix = $options;
            $options = [];
        }
        else if (is_array($options) && isset($options['prefix'])) {
            $prefix = $options['prefix'];
        }

        $subCollection = new RouteCollection();

        $subRouter = new self();
        $subRouter
            ->setInGroup(true)
            ->setRoutes($subCollection);

        $callback($subRouter, $subCollection);

        if ($prefix) {
            $subCollection->addPrefix($prefix);
        }

        if ($options) {
            if (isset($options['domain'])) {
                $subCollection->setHost($options['domain']);
            }

            if (isset($options['schemes'])) {
                $subCollection->setSchemes($options['schemes']);
            }

            if (isset($options['methods'])) {
                $subCollection->setMethods($options['methods']);
            }

            if (isset($options['defaults'])) {
                $subCollection->addDefaults($options['defaults']);
            }

            if (isset($options['options'])) {
                $subCollection->addOptions($options['options']);
            }

            if (isset($options['requirements'])) {
                $subCollection->addRequirements($options['requirements']);
            }
        }

        $this->getRoutes()->addCollection($subCollection);

        return $this;
    }

    /**
     * Set whether we are in a route group or not
     *
     * @param boolean $inGroup
     * @return $this
     */
    public function setInGroup($inGroup)
    {
        $this->inGroup = $inGroup;
        return $this;
    }

    /**
     * Get whether we are in a route group or not
     *
     * @return boolean
     */
    public function getInGroup()
    {
        return $this->inGroup;
    }
}

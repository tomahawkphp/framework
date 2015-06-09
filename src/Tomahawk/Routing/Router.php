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

use Tomahawk\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    /**
     * Whether we're in a route section
     *
     * @var bool
     */
    protected $inSection = false;
    /**
     * @var RouteCollection
     */
    protected $routes;

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
    public function get($path, $name, $callback = null, array $schemes = array())
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
    public function post($path, $name, $callback = null, array $schemes = array())
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
    public function put($path, $name, $callback = null, array $schemes = array())
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
    public function patch($path, $name, $callback = null, array $schemes = array())
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
    public function options($path, $name, $callback = null, array $schemes = array())
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
    public function delete($path, $name, $callback = null, array $schemes = array())
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
    public function any($path, $name, $callback = null, array $schemes = array())
    {
        return $this->createRoute(array(), $path, $name, $callback, $schemes);
    }

    /**
     * @param $method
     * @param $path
     * @param $name
     * @param $callback
     * @param array $schemes
     * @return Route
     */
    public function createRoute($method, $path, $name, $callback, array $schemes = array())
    {
        $methods = is_array($method) ? $method : explode('|', $method);

        if ('/' !== $path) {
            $path = $this->formatPath($path); //Symfony always stores with a starting slash
        }

        // Create a new Route class

        $route = new Route($path,
            array(
                '_controller'   => $callback
            ),
            array(), // requirements
            array(), // options
            '', // host
            $schemes, // schemes
            $methods // methods
        );

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
     * Create a route section
     *
     * @param $name
     * @param array $options
     * @param callable $callback
     * @return $this
     */
    public function section($name, $options = array(), \Closure $callback)
    {
        $sub_collection = new RouteCollection();

        $sub_router = new self();
        $sub_router->setInSection(true);
        $sub_router->setRoutes($sub_collection);

        $callback($sub_router, $sub_collection);

        $sub_collection->addPrefix($name);
        $sub_collection->addDefaults($options);

        $this->getRoutes()->addCollection($sub_collection);

        return $this;
    }

    /**
     * Set whether we are in a route section or not
     *
     * @param boolean $inSection
     * @return $this
     */
    public function setInSection($inSection)
    {
        $this->inSection = $inSection;
        return $this;
    }

    /**
     * Get whether we are in a route section or not
     *
     * @return boolean
     */
    public function getInSection()
    {
        return $this->inSection;
    }
}

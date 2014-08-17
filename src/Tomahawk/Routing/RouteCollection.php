<?php

namespace Tomahawk\Routing;

use Symfony\Component\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    /**
     * @var Route[]
     */
    private $routes = array();

    /**
     * Adds a prefix to the path of all child routes.
     *
     * @param string $prefix       An optional prefix to add before each pattern of the route collection
     * @param array  $defaults     An array of default values
     * @param array  $requirements An array of requirements
     *
     * @api
     */
    public function addPrefix($prefix, array $defaults = array(), array $requirements = array())
    {
        $prefix = trim(trim($prefix), '/');

        if ('' === $prefix) {
            return;
        }

        foreach ($this->routes as $route) {

            if ('/' !== $route->getPath()) {
                $route->setPath('/'.$prefix.$route->getPath());
            } else {
                $route->setPath('/'.$prefix.rtrim($route->getPath(), '/'));
            }

            $route->addDefaults($defaults);
            $route->addRequirements($requirements);
        }
    }
}
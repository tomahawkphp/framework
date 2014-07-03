<?php

namespace Tomahawk\Routing;

use Tomahawk\Routing\Route;
#use Hawk\Response;
#use Hawk\Controller\Inspector;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route as BaseRoute;
//use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * Class Router
 * @package Hawk
 */
class Router {

    protected $in_section = false;
    /**
     * @var RouteCollection
     */
    protected $routes;
    /**
     * @var array
     */
    public $filters = array();

    public $verbs = array(
        'put',
        'get',
        'post',
        'delete'
    );

    public $regex = '([\w-_]+)';

    /**
     * @param RouteCollection $routes
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
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
     * @return Route
     */
    public function get( $path, $name, $callback = null )
    {
        return $this->createRoute( 'GET', $path, $name, $callback );
    }

    /**
     * POST Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @return Route
     */
    public function post( $path, $name, $callback = null )
    {
        return $this->createRoute( 'POST', $path, $name, $callback );
    }

    /**
     * ANY Route
     *
     * @param $path
     * @param $name
     * @param null $callback
     * @return Route
     */
    public function any( $path, $name, $callback = null )
    {
        return $this->createRoute( array(), $path, $name, $callback );
    }

    /**
     * @param $method
     * @param $path
     * @param $name
     * @param $callback
     * @param bool $https
     * @return Route
     */
    public function createRoute( $method, $path, $name, $callback, $https = false )
    {
        $methods = is_array( $method ) ? $method : explode('|', $method);

        if( $path !== '/' )
        {
            $path = $this->formatPath($path); //Symfony always stores with a starting slash
        }

        /*if ($this->getInSection()) {
            $path = trim($path,'/');
        }*/

        $route = new Route($path,
            array(
                //'_controller' => 'MyController@method', //Default Values
                '_controller'   => $callback
            ),
            array(), // requirements
            array(), // options
            '', // host
            array(), // schemes
            $methods // methods
        );

        $this->routes->add($name, $route);

        return $route;
    }

    /**
     * Format Request Path
     *
     * @param $path
     * @return string
     */
    public function formatPath( $path )
    {
        $path = trim($path, '/') . '/';

        return $path;
    }

    public function section($name, $options = array(), \Closure $callback)
    {

        $sub_collection = new RouteCollection();

        $sub_router = new self();
        $sub_router->setInSection(true);
        $sub_router->setRoutes($sub_collection);

        $callback($sub_router, $sub_collection);

        $sub_collection->addPrefix($name);
        $sub_collection->addDefaults($options);

        /*$sub_collection->addRequirements(array(
            $name => '^(.*)$'
        ));*/

        $this->getRoutes()->addCollection($sub_collection);
    }

    /**
     * Filter
     *
     * @param $name
     * @param $callback
     */
    public function filter( $name, $callback )
    {
        $this->filters[$name] = $callback;
    }

    /**
     * Call Filter
     *
     * @param $name
     * @return mixed
     */
    public function callFilter($name)
    {
        if (!$this->filters[$name])
        {
            return false;
        }

        return call_user_func($this->filters[$name]);
    }

    /**
     * @param boolean $in_section
     */
    public function setInSection($in_section)
    {
        $this->in_section = $in_section;
    }

    /**
     * @return boolean
     */
    public function getInSection()
    {
        return $this->in_section;
    }


}
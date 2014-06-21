<?php

use Tomahawk\Routing\Router;
use Symfony\Component\Routing\RouteCollection;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testRoute()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            return 'Test';
        });

        $this->assertCount(1, $router->getRoutes()->getIterator());
    }

    public function testMultipleRoutes()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);
        $router->get('/', 'home', function() {
            return 'Home';
        });

        $router->get('/blog', 'blog', function() {
            return 'Blog';
        });

        $this->assertCount(2, $router->getRoutes()->getIterator());
    }

}
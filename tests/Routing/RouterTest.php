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
        $router->any('/', 'home', function() {
            return 'Home';
        });

        $router->get('user/{user_id}', 'user', function() {
            return 'User';
        });

        $router->post('user', 'user_post', function() {
            return 'User Post';
        });

        $this->assertCount(3, $router->getRoutes()->getIterator());
    }

    public function testInSection()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $test = $this;

        $router->section('admin', array(), function(Router $router) use ($test) {

            $test->assertTrue($router->getInSection());

            $router->any('/', 'admin_home', function() {
                return 'Home';
            });
        });

    }

}
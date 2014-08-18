<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Routing\Router;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouterTest extends TestCase
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

    public function testRouteWithRequirements()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $route = $router->get('user/{user_id}', 'user_edit', function() {
            return 'Test';
        });

        $route->where('user_id', '[0-9]+')
            ->setDefaultParameter('section', 'staff');

        $this->assertEquals('[0-9]+', $route->getRequirement('user_id'));
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

        $this->assertEquals('/admin', $router->getRoutes()->get('admin_home')->getPath());

    }

    public function testInSectionWithCollection()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $test = $this;

        $router->section('admin', array(), function(Router $router, RouteCollection $collection) use ($test) {

            $test->assertTrue($router->getInSection());

            $router->any('/', 'admin_home', function() {
                return 'Home';
            });

            $collection->setSchemes(array(
                'https'
            ));
        });


        $adminRoute = $router->getRoutes()->get('admin_home');

        $this->assertEquals(array('https'), $adminRoute->getSchemes());
        $this->assertEquals('/admin', $router->getRoutes()->get('admin_home')->getPath());

    }

}
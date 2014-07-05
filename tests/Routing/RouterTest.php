<?php

use Tomahawk\Routing\Router;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function testRouteWithRequirements()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $route = $router->get('user/{user_id}', 'user_edit', function() {
            return 'Test';
        });

        $route->where('user_id', '[0-9]+')
            ->setDefaultParameter('section', 'staff')
            ->setBeforeFilters('auth')
            ->setAfterFilters('log');


        $this->assertEquals('[0-9]+', $route->getRequirement('user_id'));
        $this->assertEquals('auth', $route->getBeforeFilters());
        $this->assertEquals('log', $route->getAfterFilters());
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

    public function testFilters()
    {
        $routeCollection = new RouteCollection();
        $router = new Router();
        $router->setRoutes($routeCollection);

        $router->beforeFilter('foo', function(Request $request, Response $response) {
            $response->setContent('bar');

            return $response;
        });

        $router->afterFilter('bar', function(Request $request) {

        });

        $router->afterFilter('baz', function(Request $request) {

        });

        $request = Request::createFromGlobals();
        $response = new Response();
        $response->setContent('home');

        $this->assertCount(1, $router->getBeforeFilters());
        $this->assertCount(2, $router->getAfterFilters());

        $result = $router->callBeforeFilter('foo', $request, $response);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals('bar', $result->getContent());

        $router->callAfterFilter('bar', $request);


        $this->assertFalse($router->callBeforeFilter('non_existent', $request, $response));
        $this->assertFalse($router->callAfterFilter('non_existent', $request));


    }

}
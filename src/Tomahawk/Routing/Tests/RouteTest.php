<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Routing\Route;

class RouteTest extends TestCase
{
    public function testConstructor()
    {
        $route = new Route('/{foo}', array('foo' => 'bar'), array('foo' => '\d+'), array('foo' => 'bar'), '{locale}.example.com');
        $this->assertEquals('/{foo}', $route->getPath(), '__construct() takes a path as its first argument');
        $this->assertEquals(array('foo' => 'bar'), $route->getDefaults(), '__construct() takes defaults as its second argument');
        $this->assertEquals(array('foo' => '\d+'), $route->getRequirements(), '__construct() takes requirements as its third argument');
        $this->assertEquals('bar', $route->getOption('foo'), '__construct() takes options as its fourth argument');
        $this->assertEquals('{locale}.example.com', $route->getHost(), '__construct() takes a host pattern as its fifth argument');

        $route = new Route('/', array(), array(), array(), '', array('Https'), array('POST', 'put'), 'context.getMethod() == "GET"');
        $this->assertEquals(array('https'), $route->getSchemes(), '__construct() takes schemes as its sixth argument and lowercases it');
        $this->assertEquals(array('POST', 'PUT'), $route->getMethods(), '__construct() takes methods as its seventh argument and uppercases it');
        $this->assertEquals('context.getMethod() == "GET"', $route->getCondition(), '__construct() takes a condition as its eight argument');

        $route = new Route('/', array(), array(), array(), '', 'Https', 'Post');
        $this->assertEquals(array('https'), $route->getSchemes(), '__construct() takes a single scheme as its sixth argument');
        $this->assertEquals(array('POST'), $route->getMethods(), '__construct() takes a single method as its seventh argument');
    }

    public function testPath()
    {
        $route = new Route('/{foo}');
        $route->setPath('/{bar}');
        $this->assertEquals('/{bar}', $route->getPath(), '->setPath() sets the path');
        $route->setPath('');
        $this->assertEquals('/', $route->getPath(), '->setPath() adds a / at the beginning of the path if needed');
        $route->setPath('bar');
        $this->assertEquals('/bar', $route->getPath(), '->setPath() adds a / at the beginning of the path if needed');
        $this->assertEquals($route, $route->setPath(''), '->setPath() implements a fluent interface');

        $route->setPath('//path');
        $this->assertEquals('/path', $route->getPath(), '->setPath() does not allow two slashes "//" at the beginning of the path as it would be confused with a network path when generating the path from the route');

        $route->setPath('/user/');
        $this->assertEquals('/user', $route->getPath());

    }

    public function testRequiresHttp()
    {
        $route = new Route('/');
        $route->requiresHttp();

        $this->assertEquals(['http'], $route->getSchemes());
    }

    public function testRequiresHttps()
    {
        $route = new Route('/');
        $route->requiresHttps();

        $this->assertEquals(['https'], $route->getSchemes());
    }

}

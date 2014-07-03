<?php

use Tomahawk\DI\Container;
use Tomahawk\Routing\Router;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Routing\Controller\ControllerResolver;

class ControllerResolverTest extends PHPUnit_Framework_TestCase
{
    public function testArrayCache()
    {
        $container = new Container();
        $controllerResolver = new ControllerResolver($container);

    }
}
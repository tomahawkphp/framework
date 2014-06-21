<?php

use Tomahawk\DI\DIContainer;
use Tomahawk\Routing\Router;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Routing\Controller\ControllerResolver;

class ControllerResolverTest extends PHPUnit_Framework_TestCase
{
    public function testArrayCache()
    {
        $container = new DIContainer();
        $controllerResolver = new ControllerResolver($container);

    }
}
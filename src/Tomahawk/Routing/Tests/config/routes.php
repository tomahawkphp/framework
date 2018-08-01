<?php

$router = new \Tomahawk\Routing\Router();
$router->setRoutes(new \Symfony\Component\Routing\RouteCollection());

$router->get('/', 'home', function() {

});

return $router->getRoutes();
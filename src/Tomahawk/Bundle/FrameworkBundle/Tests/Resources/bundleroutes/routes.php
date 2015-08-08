<?php

$router = new \Tomahawk\Routing\Router();
$router->setRoutes(new \Symfony\Component\Routing\RouteCollection());
$router->get('/account', 'account', function() {

});

return $router->getRoutes();
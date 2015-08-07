<?php

namespace Tomahawk\HttpKernel\Test\Bundles\RouteBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;

class RouteBundle extends Bundle
{
    public function boot()
    {
        //$this->container->set('event_dispatcher')
    }

    /**
     * File path to load routes from
     *
     * /dir/to/routes.php
     *
     * @return mixed
     */
    public function getRoutePath()
    {
        return 'dir/to/routes.php';
    }
}

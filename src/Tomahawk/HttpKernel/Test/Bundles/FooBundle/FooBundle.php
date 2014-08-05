<?php

namespace Tomahawk\HttpKernel\Test\Bundles\FooBundle;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;

class FooBundle extends Bundle
{
    public function getContainer()
    {
        return $this->container;
    }

    public function boot()
    {

    }

    public function shutdown()
    {
        $this->container = null;
    }
}
<?php

namespace Tomahawk\HttpKernel\Test\Bundles\FooBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;

class FooBundle extends Bundle
{
    public function getContainer()
    {
        return $this->container;
    }

    public function shutdown()
    {
        $this->container = null;
    }
}

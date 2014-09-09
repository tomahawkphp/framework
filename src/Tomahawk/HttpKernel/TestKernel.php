<?php

namespace Tomahawk\HttpKernel;

use Tomahawk\HttpKernel\Test\Bundles\FooBundle\FooBundle;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new FooBundle(),
        );

        return $bundles;
    }

    public function registerMiddleware()
    {
        return array();
    }

}

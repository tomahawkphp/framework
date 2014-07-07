<?php

use Tomahawk\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Tomahawk\HttpKernel\Tests\Bundles\FooBundle\FooBundle(),
        );

        return $bundles;
    }

}
<?php

use Tomahawk\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();
        $bundles[] = new \Tomahawk\Bundles\WebProfilerBundle\WebProfilerBundle();

        return $bundles;
    }

}
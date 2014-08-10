<?php

namespace Tomahawk\HttpKernel\Test;

use Tomahawk\HttpKernel\Kernel as BaseKernel;

use Tomahawk\HttpKernel\Test\Bundles\BarBundle\BarBundle;
use Tomahawk\HttpKernel\Test\Bundles\FooBundle\FooBundle;

class KernelForTestWithBundles extends BaseKernel
{
    public function registerBundles()
    {
        $bundles = array();

        $bundles[] = new BarBundle();
        $bundles[] = new FooBundle();

        return $bundles;
    }

    public function registerMiddleware()
    {
        return array();
    }

}

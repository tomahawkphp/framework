<?php

namespace Tomahawk\HttpKernel\Test;

use Tomahawk\HttpKernel\Kernel as BaseKernel;
use Tomahawk\HttpKernel\Test\Bundles\FooBundle\FooBundle;

class KernelStub extends BaseKernel
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

    public function getParameters()
    {
        return $this->getKernelParameters();
    }

    public function getHttpKernelInstance()
    {
        return $this->getHttpKernel();
    }
}
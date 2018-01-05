<?php

namespace Tomahawk\HttpKernel\Test;

use Tomahawk\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles()
    {
        return array();
    }

    public function isBooted()
    {
        return $this->booted;
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel\Test;

use Tomahawk\HttpKernel\Kernel as BaseKernel;
use Tomahawk\HttpKernel\Test\Bundles\BarBundle\BarBundle;
use Tomahawk\HttpKernel\Test\Bundles\EventBundle\EventBundle;

class KernelWithBundleEvents extends BaseKernel
{
    protected $eventDispatcher;

    public function registerBundles()
    {
        $bundles = array(
            new EventBundle(),
            new BarBundle(),
        );
        return $bundles;
    }

    public function getBundleMap()
    {
        return $this->bundleMap;
    }

    public function isBooted()
    {
        return $this->booted;
    }
}

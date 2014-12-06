<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

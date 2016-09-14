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
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
        return $this;
    }

    public function registerBundles()
    {
        $bundles = array(
            new FooBundle(),
        );

        return $bundles;
    }

    protected function registerEvents()
    {

    }
}

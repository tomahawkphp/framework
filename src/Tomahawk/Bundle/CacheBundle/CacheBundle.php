<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\CacheBundle;

use Tomahawk\Bundle\CacheBundle\DependencyInjection\CacheServiceProvider;
use Tomahawk\HttpKernel\Bundle\Bundle;

class CacheBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new CacheServiceProvider());
    }
}

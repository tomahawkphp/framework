<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\GeneratorBundle;

use Tomahawk\Bundle\GeneratorBundle\DependencyInjection\GeneratorServiceProvider;
use Tomahawk\HttpKernel\Bundle\Bundle;

class GeneratorBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new GeneratorServiceProvider($this->getPath() . '/Resources/skeleton'));
    }
}

<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle;

use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\Bundle\DoctrineBundle\DependencyInjection\DoctrineServiceProvider;

class DoctrineBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new DoctrineServiceProvider());
    }
}

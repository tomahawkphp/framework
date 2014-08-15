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

use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\GeneratorBundle\Generator\ControllerGenerator;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\Bundle\GeneratorBundle\Generator\ModelGenerator;

class GeneratorBundle extends Bundle
{
    public function boot()
    {
        $skeltonDirs = $this->getPath() . '/Resources/skeleton';

        $this->container->set('model_generator', function(ContainerInterface $c) use ($skeltonDirs) {

            $generator = new ModelGenerator(new Filesystem());

            $generator->setSkeletonDirs($skeltonDirs);
            return $generator;
        });

        $this->container->set('controller_generator', function(ContainerInterface $c) use ($skeltonDirs) {

            $generator = new ControllerGenerator(new Filesystem());

            $generator->setSkeletonDirs($skeltonDirs);
            return $generator;
        });
    }
}
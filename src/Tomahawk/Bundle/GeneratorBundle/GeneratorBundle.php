<?php

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
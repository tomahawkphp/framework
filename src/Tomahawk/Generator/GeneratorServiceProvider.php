<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Generator;

use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;

/**
 * Class GeneratorServiceProvider
 *
 * @package Tomahawk\Generator
 */
class GeneratorServiceProvider implements ServiceProviderInterface
{
    protected $dirs;

    public function __construct($dirs)
    {
        $this->dirs = $dirs;
    }

    public function register(ContainerInterface $container)
    {
        $skeletonDirs = $this->dirs;

        $container->set('model_generator', function(ContainerInterface $c) use ($skeletonDirs) {
            $generator = new ModelGenerator($c['filesystem']);
            $generator->setSkeletonDirs($skeletonDirs);
            return $generator;
        });

        $container->set('controller_generator', function(ContainerInterface $c) use ($skeletonDirs) {
            $generator = new ControllerGenerator($c['filesystem']);
            $generator->setSkeletonDirs($skeletonDirs);
            return $generator;
        });
    }
}

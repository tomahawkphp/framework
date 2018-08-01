<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Generator\Command;

use Symfony\Component\Console\Command\Command;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;
use Tomahawk\HttpKernel\Kernel;

/**
 * Class GenerateCommand
 *
 * @package Tomahawk\Generator\Command
 */
abstract class GenerateCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->container->get('kernel');
    }
}

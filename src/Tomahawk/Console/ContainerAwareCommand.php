<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Console;

use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Console\Command\Command;

abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}

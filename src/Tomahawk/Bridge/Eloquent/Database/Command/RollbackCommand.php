<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bridge\Eloquent\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Exception\IOException;
use Tomahawk\Bundle\EloquentBundle\Migrator\MigrationGenerator;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\Kernel;

/**
 * Class RollbackCommand
 * @package Tomahawk\Bridge\Eloquent\Command
 */
class RollbackCommand extends BaseCommand
{

}

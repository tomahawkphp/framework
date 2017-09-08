<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\DependencyInjection\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 * @package Tomahawk\DependencyInjection\Exception
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{

}

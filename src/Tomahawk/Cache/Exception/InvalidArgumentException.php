<?php

namespace Tomahawk\Cache\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

/**
 * Class InvalidArgumentException
 * @package Tomahawk\Cache\Exception
 */
class InvalidArgumentException extends BaseInvalidArgumentException implements CacheInvalidArgumentException
{

}

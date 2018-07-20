<?php

namespace Tomahawk\Log;

use Psr\Log\LoggerInterface;

/**
 * Interface LogManagerInterface
 *
 * @package Tomahawk\Log
 */
interface LogManagerInterface extends LoggerInterface
{
    /**
     * @param string $name
     * @return LoggerInterface
     */
    public function driver(string $name);
}

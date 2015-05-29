<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\CommandBus;

interface CommandHandlerInterface
{
    /**
     * Handle the command
     *
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);
}

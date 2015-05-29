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

use Tomahawk\CommandBus\Exception\HandlerNotFoundException;

class CommandBus implements CommandBusInterface
{
    protected $handlerResolver;

    /**
     * @param CommandHandlerResolverInterface $handlerResolver
     */
    public function __construct(CommandHandlerResolverInterface $handlerResolver)
    {
        $this->handlerResolver = $handlerResolver;
    }

    /**
     * Handle the command
     *
     * @param CommandInterface $command
     * @throws HandlerNotFoundException
     */
    public function handle(CommandInterface $command)
    {
        if (!$commandHandler = $this->handlerResolver->resolve($command)) {
            throw new HandlerNotFoundException();
        }

        $commandHandler->handle($command);
    }
}

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

use Tomahawk\DI\ContainerInterface;

class CommandHandlerResolver implements CommandHandlerResolverInterface
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve handler for command
     *
     * @param CommandInterface $command
     * @return CommandHandlerInterface
     */
    public function resolve(CommandInterface $command)
    {
        $handlerClass = $this->getHandlerClass($command);

        try {
            $handler = $this->container->get($handlerClass);
            return $handler;
        }
        catch(\Exception $e) {
            return null;
        }
    }

    /**
     * Get command handler class name
     *
     * @param CommandInterface $command
     * @return string
     */
    protected function getHandlerClass(CommandInterface $command)
    {
        $class = get_class($command);

        return $class .'Handler';
    }
}

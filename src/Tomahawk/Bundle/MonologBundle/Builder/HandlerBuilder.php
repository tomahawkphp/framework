<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MonologBundle\Builder;

use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

class HandlerBuilder
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $defaultHandlers;

    public function __construct(ConfigInterface $config, ContainerInterface $container, array $defaultHandlers = [])
    {
        $this->config = $config;
        $this->container = $container;
        $this->defaultHandlers = $defaultHandlers;
    }

    public function build($handler)
    {
        // Get registered handlers
        $handlers = $this->config->get('logging.custom_handlers');

        // Is it a default handler
        if (isset($this->defaultHandlers[$handler])) {
            $handlerService = $this->defaultHandlers[$handler];
        }
        else {

            if ( ! isset($handlers[$handler])) {
                throw new \InvalidArgumentException(sprintf('Unknown log handler "%s". Have you added it to the logging config?', $handler));
            }

            $handlerService = $handlers[$handler];
        }

        if ( ! isset($this->container[$handlerService])) {
            throw new \InvalidArgumentException(sprintf('Log handler "%s" not registered under "%s"', $handler, $handlerService));
        }

        return $this->container[$handlerService];
    }
}

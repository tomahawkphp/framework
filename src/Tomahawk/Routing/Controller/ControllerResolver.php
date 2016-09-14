<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The is based on code originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Controller;

use ReflectionClass;
use Psr\Log\LoggerInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ControllerNameParser
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface $logger A LoggerInterface instance
     * @param ControllerNameParser $parser
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger = null, ControllerNameParser $parser = null)
    {
        $this->container = $container;
        $this->parser = $parser;
        parent::__construct($logger);
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     *
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count && $this->parser) {
                // controller in the a:b:c notation then
                $controller = $this->parser->parse($controller);
            }
            else if (1 == $count) {
                // controller in the service:method notation
                list($service, $method) = explode(':', $controller, 2);
                return array($this->container->get($service), $method);
            }
            else if ($this->container->has($controller) && method_exists($service = $this->container->get($controller), '__invoke')) {
                return $service;
            }
            else {
                throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
            }
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $controller = $this->instantiateController($class);

        return array($controller, $method);
    }

    /**
     * Returns an instantiated controller
     *
     * @param string $class A class name
     *
     * @return object
     */
    protected function instantiateController($class)
    {
        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if ($constructor && $constructor->getParameters()) {
            $controller = $this->container->get($class);
        }
        else {
            $controller = new $class();
        }

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return $controller;
    }
}

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

use Psr\Log\LoggerInterface;
use ReflectionClass;
use Tomahawk\DI\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var \Tomahawk\DI\ContainerInterface
     */
    protected $container;

    /**
     * @var ControllerNameParser
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \Tomahawk\DI\ContainerInterface $container
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

        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if ($constructor && $constructor->getParameters()) {
            return array($this->container->build($class), $method);
        }

        // Is controller in the DI Container
        //$controller = $this->container->get($class);

        /*if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }*/

        //return array($controller, $method);

        return array($this->instantiateController($class), $method);
    }
}

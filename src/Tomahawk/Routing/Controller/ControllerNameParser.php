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

use Tomahawk\HttpKernel\KernelInterface;

/**
 * Class ExceptionListener
 *
 * Based on the Symfony2 FrameworkBundle ControllerNameParser
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @package Tomahawk\HttpKernel\Event
 */
class ControllerNameParser
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Converts a short notation a:b:c to a class::method.
     *
     * @param string $controller A short notation controller (a:b:c)
     *
     * @return string A string in the class::method notation
     *
     * @throws \InvalidArgumentException when the specified bundle is not enabled
     */
    public function parse($controller)
    {
        $originalController = $controller;

        if (3 !== count($parts = explode(':', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid "a:b:c" controller string.', $controller));
        }

        list($bundle, $controller, $action) = $parts;

        $controller = str_replace('/', '\\', $controller);

        $bundles = array();

        try {
            // this throws an exception if there is no such bundle
            $allBundles = $this->kernel->getBundle($bundle, false);
        }
        catch (\InvalidArgumentException $e) {
            $message = sprintf('The "%s" (from the _controller value "%s") does not exist or is not enabled in your kernel!', $bundle, $originalController);

            throw new \InvalidArgumentException($message, 0, $e);
        }

        foreach ($allBundles as $b) {
            $try = $b->getNamespace().'\\Controller\\'.$controller.'Controller';

            if (class_exists($try)) {
                return $try.'::'.$action.'Action';
            }

            $bundles[] = $b->getName();
            $msg = sprintf('The _controller value "%s:%s:%s" maps to a "%s" class, but this class was not found. Create this class or check the spelling of the class and its namespace.', $bundle, $controller, $action, $try);
        }

        if (count($bundles) > 1) {
            $msg = sprintf('Unable to find controller "%s:%s" in bundles %s.', $bundle, $controller, implode(', ', $bundles));
        }

        throw new \InvalidArgumentException($msg);

    }

    /**
     * Converts a class::method notation to a short one (a:b:c).
     *
     * @param string $controller A string in the class::method notation
     *
     * @return string A short notation controller (a:b:c)
     *
     * @throws \InvalidArgumentException when the controller is not valid or cannot be found in any bundle
     */
    public function build($controller)
    {
        if (0 === preg_match('#^(.*?\\\\Controller\\\\(.+))Controller::(.+)Action#', $controller, $match)) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid "class::method" string.', $controller));
        }

        $className = $match[1];

        $controllerName = $match[2];

        $actionName = $match[3];

        foreach ($this->kernel->getBundles() as $name => $bundle) {

            if (0 !== strpos($className, $bundle->getNamespace())) {
                continue;
            }

            return sprintf('%s:%s:%s', $name, $controllerName, $actionName);
        }

        throw new \InvalidArgumentException(sprintf('Unable to find a bundle that defines controller "%s".', $controller));
    }
}

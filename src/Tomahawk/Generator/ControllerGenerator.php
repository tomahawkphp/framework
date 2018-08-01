<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Generator;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates a Controller inside a bundle.
 *
 * @author Tom Ellis
 *
 * Based on Sensio Labs Controller Generator
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class ControllerGenerator extends Generator
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem A Filesystem instance
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $directory
     * @param string $namespace
     * @param string $controller
     * @param array $actions
     */
    public function generate(string $directory, string $namespace, string $controller, array $actions = [])
    {
        $controllerFile = $directory.'/'.$controller.'.php';

        if (file_exists($controllerFile)) {
            throw new \RuntimeException(sprintf('Controller "%s" already exists', $controller));
        }

        $parameters = [
            'namespace' => $namespace,
            'controller' => $controller,
            'actions'    => $actions
        ];

        $this->renderFile('controller/Controller.php.twig', $controllerFile, $parameters);
    }
}

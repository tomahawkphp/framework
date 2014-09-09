<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\GeneratorBundle\Generator;

use Tomahawk\Generator\Generator;
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

    public function generate(BundleInterface $bundle, $controller, array $actions = array())
    {
        $dir = $bundle->getPath();

        $controllerFile = $dir.'/Controller/'.$controller.'Controller.php';
        if (file_exists($controllerFile)) {
            throw new \RuntimeException(sprintf('Controller "%s" already exists', $controller));
        }

        $parameters = array(
            'namespace'  => $bundle->getNamespace(),
            'bundle'     => $bundle->getName(),
            'controller' => $controller,
            'actions'    => $actions
        );

        $this->renderFile('controller/Controller.php.twig', $controllerFile, $parameters);
    }
}

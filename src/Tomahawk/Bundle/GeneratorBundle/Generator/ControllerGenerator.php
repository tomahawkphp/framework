<?php

namespace Tomahawk\Bundle\GeneratorBundle\Generator;

use Tomahawk\Generator\Generator;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\DI\Container;

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
        );

        /*foreach ($actions as $i => $action) {

            // get the actioname without the sufix Action (for the template logical name)
            $actions[$i]['basename'] = substr($action['name'], 0, -6);
            $params = $parameters;
            $params['action'] = $actions[$i];

            // create a template
            $template = $actions[$i]['template'];
            if ('default' == $template) {
                $template = $bundle->getName().':'.$controller.':'.substr($action['name'], 0, -6).'.html.'.$templateFormat;
            }

            if ('twig' == $templateFormat) {
                $this->renderFile('controller/Template.html.twig.twig', $dir.'/Resources/views/'.$this->parseTemplatePath($template), $params);
            } else {
                $this->renderFile('controller/Template.html.php.twig', $dir.'/Resources/views/'.$this->parseTemplatePath($template), $params);
            }

        }*/

        $parameters['actions'] = $actions;

        $this->renderFile('controller/Controller.php.twig', $controllerFile, $parameters);
        //$this->renderFile('controller/ControllerTest.php.twig', $dir.'/Tests/Controller/'.$controller.'ControllerTest.php', $parameters);
    }
}
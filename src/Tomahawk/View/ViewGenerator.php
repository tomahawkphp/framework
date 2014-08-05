<?php

namespace Tomahawk\View;

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Templating\Loader\TemplateLocator;
use Tomahawk\Templating\TemplateNameParser;
use Tomahawk\Templating\Loader\FilesystemLoader;
use Tomahawk\HttpKernel\Config\FileLocator;
use Tomahawk\DI\ContainerInterface;

class ViewGenerator implements ViewGeneratorInterface
{
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    public $engine;

    public $shared = array();

    /**
     * @var \Tomahawk\DI\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $helpers = array();

    public function __construct(EngineInterface $engine, ContainerInterface $container)
    {
        $this->engine = $engine;
        $this->container = $container;
    }

    public function render($view, array $data = array())
    {
        return $this->engine->render($view, $data);
    }

    public function share($name, $value)
    {
        $this->shared[$name] = $value;
    }

    public function getShared($name = null)
    {
        if (null === $name) {
            return $this->shared;
        }

        return $this->shared[$name];
    }


    /**
     * Setup the View templating
     */
    protected function setup()
    {
        $locator = new FileLocator($this->container->get('kernel'));
        $templateLocator = new TemplateLocator($locator);

        $loader = new FilesystemLoader($templateLocator);
        //$loader = new FilesystemLoader($this->directoryPathPatterns);

        $this->templating = new PhpEngine(new TemplateNameParser($this->container->get('kernel')), $loader);
        $this->templating->addHelpers($this->helpers);
    }

}

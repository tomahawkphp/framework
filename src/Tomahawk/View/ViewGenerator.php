<?php

namespace Tomahawk\View;

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;
//use Tomahawk\Templating\TemplateNameParser;
use Tomahawk\Templating\TemplateFileNameParser;

class ViewGenerator implements ViewGeneratorInterface
{
    /**
     * @var \Symfony\Component\Templating\PhpEngine
     */
    public $templating;

    public $shared = array();

    public static $instance;

    protected $directoryPathPatterns = array();

    /**
     * @var array
     */
    protected $helpers = array();

    public function __construct($directoryPathPatterns, array $helpers = array())
    {
        $this->directoryPathPatterns = $directoryPathPatterns;
        $this->helpers = $helpers;
        $this->setup();
    }

    public function render($view, array $data = array())
    {
        $data = array_merge($this->getShared(), $data);
        return $this->templating->render($view, $data);
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
     * @param array $directoryPathPatterns
     * @return $this
     */
    public function setDirectoryPathPatterns($directoryPathPatterns)
    {
        $this->directoryPathPatterns = $directoryPathPatterns;
        $this->setup();
        return $this;
    }

    /**
     * @return array
     */
    public function getDirectoryPathPatterns()
    {
        return $this->directoryPathPatterns;
    }

    /**
     * Setup the View templating
     */
    protected function setup()
    {
        $loader = new FilesystemLoader($this->directoryPathPatterns);
        $this->templating = new PhpEngine(new TemplateNameParser(), $loader);
        $this->templating->addHelpers($this->helpers);
    }

}

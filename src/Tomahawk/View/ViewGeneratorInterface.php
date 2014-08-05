<?php

namespace Tomahawk\View;

use \Symfony\Component\Templating\PhpEngine;
use \Symfony\Component\Templating\TemplateNameParser;
use \Symfony\Component\Templating\Loader\FilesystemLoader;

interface ViewGeneratorInterface
{
    public function render($view, array $data = array());

    public function share($name, $value);

    public function getShared($name = null);
}

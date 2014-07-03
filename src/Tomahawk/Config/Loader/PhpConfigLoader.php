<?php

namespace Tomahawk\Config\Loader;

use Symfony\Component\Config\Loader\FileLoader;

class PhpConfigLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        $configValues = require($resource);

        return $configValues;
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
<?php

namespace Tomahawk\Routing;

use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute {

    public function where($parameter, $pattern)
    {
        $this->setRequirement($parameter, $pattern);
        return $this;
    }

    public function setDefaultParameter($name, $value)
    {
        $this->setDefault($name, $value);
        return $this;
    }

    public function controller($callback)
    {
        $this->setDefault('_controller', $callback);
        return $this;
    }


    public function beforeFilters($name)
    {
        $this->setDefault('_beforeFilters', $name);
        return $this;
    }

    public function afterFilters($name)
    {
        $this->setDefault('_afterFilters', $name);
        return $this;
    }
}
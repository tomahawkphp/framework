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

    public function setBeforeFilters($name)
    {
        $this->setDefault('_beforeFilters', $name);
        return $this;
    }

    public function getBeforeFilters()
    {
        return $this->getDefault('_beforeFilters');
    }

    public function setAfterFilters($name)
    {
        $this->setDefault('_afterFilters', $name);
        return $this;
    }

    public function getAfterFilters()
    {
        return $this->getDefault('_afterFilters');
    }
}
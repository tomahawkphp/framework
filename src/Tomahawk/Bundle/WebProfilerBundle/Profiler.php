<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

class Profiler
{
    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @return $this
     */
    public function enable()
    {
        $this->enabled = 1;
        return $this;
    }

    /**
     * @return $this
     */
    public function disable()
    {
        $this->enabled = 0;
        return $this;
    }
}
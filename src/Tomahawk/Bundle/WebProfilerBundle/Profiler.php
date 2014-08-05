<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Symfony\Component\Templating\EngineInterface;

class Profiler
{
    /**
     * An array of the recorded queries.
     *
     * @var array
     */
    protected $queries = array();

    /**
     * An array of the recorded logs.
     *
     * @var array
     */
    protected $logs = array();

    /**
     * An array of the recorded timers.
     *
     * @var array
     */
    protected $timers = array();

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $engine;

    /**
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Enable Profiler
     *
     * @return $this
     */
    public function enable()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Is Profiler enabled
     *
     * @return bool
     */
    public function enabled()
    {
        return true === $this->enabled;
    }

    /**
     * Disable Profiler
     *
     * @return $this
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Is Profiler disabled
     *
     * @return bool
     */
    public function disabled()
    {
        return false === $this->enabled;
    }

    /**
     * Render Template
     *
     * @return string
     */
    public function render()
    {
        return $this->engine->render('WebProfilerBundle::profiler.php');
    }
}
<?php

namespace Tomahawk\Bundle\WebProfilerBundle;

use Symfony\Component\Templating\EngineInterface;
use Illuminate\Database\DatabaseManager;
use Tomahawk\HttpKernel\Kernel;

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
     * @var
     */
    protected $assetsPath;

    /**
     * @var DatabaseManager
     */
    protected $manager;

    /**
     * @param EngineInterface $engine
     * @param DatabaseManager $manager
     * @param $assetsPath
     */
    public function __construct(EngineInterface $engine, DatabaseManager $manager, $assetsPath)
    {
        $this->engine = $engine;
        $this->assetsPath = $assetsPath;
        $this->manager = $manager;
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
     * @param array $queries
     */
    public function addQueries(array $queries)
    {
        foreach ($queries as $query) {

            foreach ($query['bindings'] as $binding) {
                $binding = $this->escape($binding);
                $query['query'] = preg_replace('/\?/', $binding, $query ['query'], 1);
            }

            $this->queries[] = $query;
        }
    }

    /**
     * Render Template
     *
     * @return string
     */
    public function render()
    {
        $memory      = $this->getFileSize(memory_get_usage(true));
        $memory_peak = $this->getFileSize(memory_get_peak_usage(true));
        $time        = number_format((microtime(true) - TOMAHAWKPHP_START) * 1000, 2);
        $timers      = $this->timers;
        foreach ($timers as &$timer) {
            $timer['running_time'] = number_format((microtime(true) - $timer['start'] ) * 1000, 2);
        }

        return $this->engine->render('WebProfilerBundle:Profiler:index', array(
            'assetsPath'  => $this->assetsPath,
            'queries'     => $this->queries,
            'logs'        => $this->logs,
            'memory'      => $memory,
            'memory_peak' => $memory_peak,
            'time'        => $time,
            'timers'      => $timers,
            'version'     => Kernel::VERSION

        ));
    }

    /**
     * Calculate the human-readable file size with units.
     *
     * @param  int     $size
     * @return string
     */
    protected function getFileSize($size)
    {
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$units[$i];
    }

    protected function escape($value)
    {
        return $this->manager->connection()->getPdo()->quote($value);
    }
}
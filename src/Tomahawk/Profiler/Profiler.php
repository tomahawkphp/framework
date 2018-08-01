<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Profiler;

use Tomahawk\HttpKernel\Kernel;
use Doctrine\DBAL\Logging\DebugStack;
use Symfony\Component\HttpFoundation\Request;
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
     * @var
     */
    protected $assetsPath;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @param EngineInterface $engine
     * @param string $assetsPath
     * @param int $startTime
     */
    public function __construct(EngineInterface $engine, $assetsPath, $startTime)
    {
        $this->engine = $engine;
        $this->assetsPath = $assetsPath;
        $this->startTime = $startTime;
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
     * @return $this
     */
    public function addQueries(array $queries)
    {
        foreach ($queries as $query) {

            foreach ($query['bindings'] as $binding) {
                $binding = $this->escape($binding);
                $query['query'] = preg_replace('/\?/', $binding, $query['query'], 1);
            }

            $this->queries[] = $query;
        }

        return $this;
    }

    /**
     * Add queries for doctrine off the DebugStack logger
     *
     * @param DebugStack $debugStack
     * @return $this
     */
    public function addDoctrineQueries(DebugStack $debugStack)
    {
        foreach ($debugStack->queries as $query) {

            if (!$query['params']) {
                $query['params'] = array();
            }

            if (!$query['types']) {
                $query['types'] = array();
            }

            // Because doctrine columns can be more advanced we need to convert them to string
            // This is a quick a dirty way of doing it so could do with going elsewhere
            $query['params'] = $this->convertDoctrineParameters($query['params'], $query['types']);

            $queries = array(
                array(
                    'query'    => $query['sql'],
                    'bindings' => $query['params'],
                    'time'     => $query['executionMS'],
                )
            );

            $this->addQueries($queries);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Add a log to the profiler
     *
     * ->addLog('info', 'Thing');
     *
     * @param $type
     * @param $value
     * @return $this
     */
    public function addLog($type, $value)
    {
        $this->logs[] = array(
            'type'  => $type,
            'value' => $value,
        );

        return $this;
    }

    /**
     * Add logs to the profiler
     *
     * ->addLogs(array(
     *       array(
     *          'type' => 'info',
     *          'value' => 'Value = bar'
     *      ),
     *      array(
     *          'type' => 'error',
     *          'value' => 'Value != bar'
     *      ),
     *   ));
     *
     * @param array $logs
     * @return $this
     */
    public function addLogs($logs = array())
    {
        foreach ($logs as $log) {
            $this->logs[] = $log;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add timers to profiler
     *
     * ->addTimers(array(
     *       'Test' => array(
     *           'start' => time(),
     *           'time' => time() + 1000,
     *       ),
     *   ));
     *
     * @param array $timers
     * @return $this
     */
    public function addTimers($timers = array())
    {
        foreach ($timers as $name => $timer) {
            $this->timers[$name] = $timer;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Render Template
     *
     * @return string
     */
    public function render()
    {
        if (!$this->enabled()) {
            return '';
        }

        $memory      = $this->getFileSize(memory_get_usage(true));
        $memory_peak = $this->getFileSize(memory_get_peak_usage(true));
        $time        = number_format((microtime(true) - $this->startTime) * 1000, 2);
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
            'version'     => Kernel::VERSION,
            'request'     => $this->getRequest(),
        ));
    }

    public function convertDoctrineParameters(array $parameters, array $types)
    {
        foreach ($parameters as $i => $parameter) {

            $type = $types[$i];

            if ('datetime' === $type) {
                $parameters[$i] = $parameter->format('Y-m-d H:i:s');
            }
            else if ('date' === $type) {
                $parameters[$i] = $parameter->format('Y-m-d');
            }
            else if ('time' === $type) {
                $parameters[$i] = $parameter->format('H:i:s');
            }
            else if ('datetimetz' === $type) {
                $parameters[$i] = $parameter->getName();
            }
            else if ('simple_array' === $type) {
                $parameters[$i] = implode(',', $parameter);
            }
            else if ('array' === $type) {
                $parameters[$i] = serialize($parameter);
            }
            else if ('json_array' === $type) {
                $parameters[$i] = json_encode($parameter);
            }
            else if ('object' === $type) {
                $parameters[$i] = serialize($parameter);
            }

        }

        return $parameters;
    }

    /**
     * Get Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Request
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
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
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$units[$i];
    }

    /**
     * @param $value
     * @return string
     */
    protected function escape($value)
    {
        return sprintf("%s", str_replace('"', '\"', $value));
    }
}

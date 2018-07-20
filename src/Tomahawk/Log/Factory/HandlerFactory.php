<?php

namespace Tomahawk\Log\Factory;

use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Class HandlerFactory
 *
 * Ideas taken from the Laravel LogManager
 *
 * @package Tomahawk\Log\Factory
 */
class HandlerFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $createdDrivers = [];

    /**
     * @var array
     */
    protected $customDrivers;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug' => Logger::DEBUG,
        'info' => Logger::INFO,
        'notice' => Logger::NOTICE,
        'warning' => Logger::WARNING,
        'error' => Logger::ERROR,
        'critical' => Logger::CRITICAL,
        'alert' => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    public function __construct(
        ContainerInterface $container,
        ConfigInterface $config,
        array $customDrivers = []
    )
    {
        $this->container = $container;
        $this->config = $config;
        $this->customDrivers = $customDrivers;
    }

    public function make(string $name)
    {
        return $this->createDriver($name);
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException
     * @return LoggerInterface
     */
    protected function createDriver(string $name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache driver [{$name}] is not defined.");
        }

        if (isset($this->customDrivers[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException("Cache driver [{$name}] is not defined.");
    }

    protected function createSingleDriver(string $name, array $config)
    {
        return new Logger($this->parseChannel($config), [
            $this->prepareHandler(
                new StreamHandler(
                    $config['path'], $this->parseLevel($config),
                    $config['bubble'] ?? true, $config['permission'] ?? null, $config['locking'] ?? false
                )
            ),
        ]);
    }

    /**
     * @param string $name
     * @param array $config
     * @return LoggerInterface
     */
    protected function callCustomCreator(string $name, array $config)
    {
        $driver = $this->customDrivers[$config['driver']]->make($name, $config);

        return $this->createdDrivers[$name] = $driver;
    }

    /**
     * Prepare the handler for usage by Monolog.
     *
     * @param \Monolog\Handler\HandlerInterface  $handler
     * @param array  $config
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function prepareHandler(HandlerInterface $handler, array $config = [])
    {
        if ( ! isset($config['formatter'])) {
            $handler->setFormatter($this->formatter());
        } elseif ($config['formatter'] !== 'default') {
            //$handler->setFormatter($this->container->get($config['formatter'], $config['formatter_with'] ?? []));
        }

        return $handler;
    }

    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function formatter()
    {
        $formatter = new LineFormatter(null, null, true, true);
        $formatter->includeStacktraces();

        return $formatter;
    }

    /**
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name)
    {
        return $this->config->get("logging.handler.{$name}");
    }

    /**
     * Convert level to mongo constant
     *
     * @param array $config
     * @return int|mixed
     */
    private function parseLevel(array $config)
    {
        $level = $config['level'] ?? 'debug';

        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }
}

<?php

namespace Tomahawk\Log\Factory;

use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

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
            throw new InvalidArgumentException("Log config [{$name}] is not defined.");
        }

        if (isset($this->customDrivers[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Log driver [{$config['driver']}] is not defined.");
    }

    /**
     * Create an aggregate log driver instance.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createStackDriver(array $config)
    {
        $handlers = [];

        foreach ($config['channels'] as $channel) {
            $handlers[] = $this->createDriver($channel)->getHandlers();
        }

        /*$handlers = collect($config['channels'])->flatMap(function ($channel) {
            return $this->createDriver($channel)->getHandlers();
        })->all();*/

        return new Logger($this->parseLoggerName($config), $handlers);
    }

    /**
     * @param array $config
     * @return Logger
     * @throws \Exception
     */
    protected function createSingleDriver(array $config)
    {
        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
                new StreamHandler(
                    $config['path'], $this->parseLevel($config),
                    $config['bubble'] ?? true, $config['permission'] ?? null, $config['locking'] ?? false
                )
            ),
        ]);
    }

    /**
     * @param array $config
     * @return Logger
     * @throws \Exception
     */
    protected function createDailyDriver(array $config)
    {
        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
                new RotatingFileHandler(
                    $config['path'], $config['days'] ?? 7, $this->parseLevel($config),
                    $config['bubble'] ?? true, $config['permission'] ?? null, $config['locking'] ?? false
                )
            ),
        ]);
    }

    /**
     * Create an instance of the syslog log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSyslogDriver(array $config)
    {
        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
                new SyslogHandler(
                    $this->config->get('app.name'), $config['facility'] ?? LOG_USER, $this->parseLevel($config)
                )
            ),
        ]);
    }

    /**
     * Create an instance of the "error log" log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createErrorlogDriver(array $config)
    {
        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
                new ErrorLogHandler(
                    $config['type'] ?? ErrorLogHandler::OPERATING_SYSTEM, $this->parseLevel($config)
                )
            ),
        ]);
    }

    /**
     * Create an instance of the stream log driver.
     *
     * @param  array $config
     * @return \Psr\Log\LoggerInterface
     * @throws \Exception
     */
    protected function createStreamDriver(array $config)
    {
        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
                new StreamHandler($config['stream'], $this->parseLevel($config))
            ),
        ]);
    }

    /**
     * Create an instance of any handler available in Monolog.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createMonologDriver(array $config)
    {
        if (! is_a($config['handler'], HandlerInterface::class, true)) {
            throw new InvalidArgumentException(
                $config['handler'].' must be an instance of '.HandlerInterface::class
            );
        }

        $with = array_merge($config['with'] ?? [], $config['handler_with'] ?? []);

        return new Logger($this->parseLoggerName($config), [
            $this->prepareHandler(
            $this->app->make($config['handler'], $with), $config
        )]);
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

            // @TODO - Create formatter factory?
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
        return $this->config->get("logging.channels.{$name}");
    }

    /**
     * Parse Logger Name
     *
     * @param array $config
     * @return int|mixed
     */
    private function parseLoggerName(array $config)
    {
        return $config['name'] ?? 'app';
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

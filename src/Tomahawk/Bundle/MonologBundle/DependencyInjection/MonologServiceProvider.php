<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MonologBundle\DependencyInjection;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\FingersCrossedHandler;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\DependencyInjection\ServiceProviderInterface;
use Tomahawk\Bundle\MonologBundle\Builder\HandlerBuilder;

class MonologServiceProvider implements ServiceProviderInterface
{
    /**
     * Default handlers
     *
     * @var array
     */
    private $defaultHandlers = [
        'fingers_crossed' => 'monolog.handler.fingers_crossed',
        'rotating_file'   => 'monolog.handler.rotating_file',
        'stream'          => 'monolog.handler.stream',
    ];

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param ContainerInterface $container An Container instance
     */
    public function register(ContainerInterface $container)
    {
        $self = $this;

        $container->set('monolog.handler.builder', function(ContainerInterface $c) {

            return new HandlerBuilder($c['config'], $c, $this->defaultHandlers);
        });

        // Register Monolog handlers

        $container->set('monolog.handler.fingers_crossed', function(ContainerInterface $c) use($self) {

            $config = $c['config'];

            $actionLevel = $config->get('logging.action_level', Logger::ERROR);
            $actionLevel = $self->levelToMonologConst($actionLevel);

            $actionHandlerName = $config->get('logging.action_handler');
            $actionHandler = $c['monolog.handler.builder']->build($actionHandlerName);

            $handler = new FingersCrossedHandler(
                $actionHandler,
                $actionLevel
            );

            return $handler;
        });

        $container->set('monolog.handler.rotating_file', function(ContainerInterface $c) use($self) {

            $config = $c['config'];
            $kernel = $c['kernel'];

            $maxFiles = $config->get('logging.max_files', 10);
            $logLevel = $config->get('logging.level', Logger::ERROR);
            $logLevel = $self->levelToMonologConst($logLevel);

            $stream = sprintf('%s/%s.log', $config->get('logging.path'), $kernel->getEnvironment());

            $formatter = new LineFormatter(null, null, true, true);
            $formatter->includeStacktraces();

            $handler = new RotatingFileHandler($stream, $maxFiles, $logLevel);
            $handler->setFormatter($formatter);

            return $handler;
        });

        $container->set('monolog.handler.stream', function(ContainerInterface $c) use($self) {

            $config = $c['config'];
            $kernel = $c['kernel'];

            $logLevel = $config->get('logging.level', Logger::ERROR);
            $logLevel = $self->levelToMonologConst($logLevel);

            $stream = sprintf('%s/%s.log', $config->get('logging.path'), $kernel->getEnvironment());

            $formatter = new LineFormatter(null, null, true, true);
            $formatter->includeStacktraces();

            $handler = new StreamHandler($stream, $logLevel);

            $handler->setFormatter($formatter);

            return $handler;
        });

        $container->set('Monolog\Handler\HandlerInterface', function(ContainerInterface $c) {
            return $c['monolog.handler.builder']->build($c['config']->get('logging.handler'));
        });

        $container->set('Psr\Log\LoggerInterface', function(ContainerInterface $c) {

            // Create a log channel
            $log = new Logger('application');
            $log->pushHandler($c['monolog.handler']);

            return $log;
        });

        $container->addAlias('monolog.handler', 'Monolog\Handler\HandlerInterface');
        $container->addAlias('monolog', 'Psr\Log\LoggerInterface');
        $container->addAlias('logger', 'Psr\Log\LoggerInterface');

    }

    /**
     * Convert level to mongo constant
     *
     * @param $level
     * @return int|mixed
     */
    private function levelToMonologConst($level)
    {
        return is_int($level) ? $level : constant('Monolog\Logger::' . strtoupper($level));
    }
}

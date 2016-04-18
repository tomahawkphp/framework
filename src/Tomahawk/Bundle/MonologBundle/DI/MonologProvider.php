<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MonologBundle\DI;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ServiceProviderInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;

class MonologProvider implements ServiceProviderInterface
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
        // Register Monolog handlers

        $container->set('monolog.handler.fingers_crossed', function(ContainerInterface $c) use($self) {

            $config = $c['config'];

            //$bubble = $config->get('logging.bubble');
            $actionLevel = $config->get('logging.action_level', Logger::ERROR);
            $actionLevel = $self->levelToMonologConst($actionLevel);

            $handler = new FingersCrossedHandler(
                $c['monolog.handler.rotating_file'],
                $actionLevel
            );

            return $handler;
        });

        $container->set('monolog.handler.rotating_file', function(ContainerInterface $c) use($self) {

            $config = $c['config'];
            $kernel = $c['kernel'];

            $bubble = $config->get('logging.bubble');
            $maxFiles = $config->get('logging.max_files', 10);
            $logLevel = $config->get('logging.level', Logger::ERROR);
            $logLevel = $self->levelToMonologConst($logLevel);


            $stream = sprintf('%s/%s.log', $config->get('logging.path'), $kernel->getEnvironment());

            $formatter = new LineFormatter(null, null, true, true);
            $formatter->includeStacktraces();

            $handler = new RotatingFileHandler($stream, $maxFiles, $logLevel, $bubble);
            $handler->setFormatter($formatter);

            return $handler;
        });

        $container->set('monolog.handler.stream', function(ContainerInterface $c) use($self) {

            $config = $c['config'];
            $kernel = $c['kernel'];

            $bubble = $config->get('logging.bubble');
            $logLevel = $config->get('logging.level', Logger::ERROR);
            $logLevel = $self->levelToMonologConst($logLevel);

            $stream = sprintf('%s/%s.log', $config->get('logging.path'), $kernel->getEnvironment());

            $formatter = new LineFormatter(null, null, true, true);
            $formatter->includeStacktraces();

            $handler = new StreamHandler($stream, $logLevel, $bubble);

            $handler->setFormatter($formatter);

            return $handler;
        });

        $container->set('Monolog\Handler\HandlerInterface', function(ContainerInterface $c) {

            $config = $c['config'];

            // Get registered handlers
            $handlers = $config->get('logging.handlers');

            // Get handler to use
            $handler = $config->get('logging.handler');

            // Is it a default handler
            if (isset($this->defaultHandlers[$handler])) {
                $handlerService = $this->defaultHandlers[$handler];
            }
            else {
                if ( ! isset($handlers[$handler])) {
                    throw new \InvalidArgumentException(sprintf('Unknown log handler "%s". Have you added it to the logging config?', $handler));
                }

                $handlerService = $handlers[$handler];
            }

            if ( ! isset($c[$handlerService])) {
                throw new \InvalidArgumentException(sprintf('Log handler "%s" not registered under "%s"', $handler, $handlerService));
            }

            return $c[$handlerService];
        });

        $container->set('Psr\Log\LoggerInterface', function(ContainerInterface $c) {

            // Create a log channel
            $log = new Logger('application');
            $log->pushHandler($c['monolog_handler']);

            return $log;
        });

        $container->addAlias('monolog_handler', 'Monolog\Handler\HandlerInterface');
        $container->addAlias('monolog', 'Psr\Log\LoggerInterface');
        $container->addAlias('logger', 'Psr\Log\LoggerInterface');

    }

    private function levelToMonologConst($level)
    {
        return is_int($level) ? $level : constant('Monolog\Logger::' . strtoupper($level));
    }
}

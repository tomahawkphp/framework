<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpKernel\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Config\ConfigInterface;

/**
 * Class ExceptionListener
 *
 * Based on the Symfony2 ExceptionListener
 *
 * @package Tomahawk\HttpKernel\Event
 */
class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @param EngineInterface $templating
     * @param string $environment
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(EngineInterface $templating, $environment, ConfigInterface $config, LoggerInterface $logger = null)
    {
        $this->templating = $templating;
        $this->environment = $environment;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof NotFoundHttpException) {

            $templatePath = $this->config->get('error.template_404', null);
            $response = new Response();

            if ($templatePath) {
                $response->setContent($this->templating->render($templatePath));
            }
            else {
                $response->setContent('404 - File Not Found');
            }

            $event->setResponse($response);
        }
        else {

            $exception = $event->getException();

            $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            if ('prod' === $this->environment) {

                $templatePath = $this->config->get('error.template_50x', null);
                $response = new Response();

                if ($templatePath) {
                    $response->setContent($this->templating->render($templatePath));
                }
                else {
                    $response->setContent('500 - Internal Server Error');
                }

                $event->setResponse($response);
            }
        }
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($exception, array('exception' => $exception));
            }
            else {
                $this->logger->error($exception, array('exception' => $exception));
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onException',
        );
    }
}

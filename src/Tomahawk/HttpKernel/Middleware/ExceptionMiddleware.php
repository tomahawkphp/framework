<?php

namespace Tomahawk\HttpKernel\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\HttpKernel\KernelInterface;
use Tomahawk\Middleware\Middleware;

class ExceptionMiddleware extends Middleware
{
    public function boot()
    {
        $environment = $this->getKernel()->getEnvironment();
        $logger = $this->getLogger();
        $templating = $this->getTemplating();

        $this->getEventDispatcher()->addListener(KernelEvents::EXCEPTION, function(GetResponseForExceptionEvent $event) use ($environment, $logger, $templating) {

            if ($event->getException() instanceof NotFoundHttpException) {

                $response = new Response();
                $response->setContent($templating->render('::Error:404'));

                $event->setResponse($response);
            }
            else {

                $exception = $event->getException();

                $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));


                if ('prod' === $environment) {

                    $response = new Response();
                    $response->setContent($templating->render('::Error:50x'));

                    $event->setResponse($response);

                }
            }

        });
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->getLogger()) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->getLogger()->critical($exception, array('exception' => $exception));
            } else {
                $this->getLogger()->error($exception, array('exception' => $exception));
            }
        }
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return EngineInterface
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->container->get('kernel');
    }
}

<?php

namespace Tomahawk\Bundle\WebProfilerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\HttpKernel\KernelInterface;

class InjectWebProfilerListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // If we're in a sub request don't append web profiler bar
        if (KernelInterface::SUB_REQUEST === $event->getRequestType()) {
            return;
        }

        if ($response = $event->getResponse()) {
            $content = $response->getContent();

            /** @var Profiler $webProfiler */
            $webProfiler = $this->container->get('web_profiler');

            $webProfiler->setRequest($event->getRequest());

            // Check if we have the query stack from doctrine
            $debugStack = $this->container->has('doctrine.query_stack') ? $this->container->get('doctrine.query_stack') : null;

            if ($debugStack) {
                $webProfiler->addDoctrineQueries($debugStack);
            }

            $renderedContent = $webProfiler->render();

            $pos = strripos($content, '</body>');

            if (false !== $pos) {
                $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
            } else {
                $content = $content . $renderedContent;
            }

            $response->setContent($content);
            $event->setResponse($response);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -128],
        ];
    }
}

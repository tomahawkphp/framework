<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;
use Tomahawk\Bundle\FrameworkBundle\EventListener\LocaleListener;
use Tomahawk\Test\TestCase;

class LocaleListenerTest extends TestCase
{
    private $requestStack;

    public function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', array(), array(), '', false);
    }

    public function testOnKernelRequestEvent()
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/', 'get');
        $dispatcher = new EventDispatcher();

        $listener = new LocaleListener('de', $this->requestStack);

        $dispatcher->addSubscriber($listener);

        $getResponseEvent = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $dispatcher->dispatch(KernelEvents::REQUEST, $getResponseEvent);

        $this->assertEquals('de', $request->getLocale());

    }

    public function testOnKernelRequestEventWithSetParameter()
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/', 'get');
        $dispatcher = new EventDispatcher();

        $request->attributes->set('_locale', 'de');
        $listener = new LocaleListener('de', $this->requestStack);

        $dispatcher->addSubscriber($listener);

        $getResponseEvent = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $dispatcher->dispatch(KernelEvents::REQUEST, $getResponseEvent);

        $this->assertEquals('de', $request->getLocale());

    }

    public function testOnKernelFinishRequestEvent()
    {
        $parentRequest = Request::create('http://localhost/', 'get');
        $parentRequest->setLocale('de');

        $this->requestStack->expects($this->once())->method('getParentRequest')->will($this->returnValue($parentRequest));

        $requestContext = new RequestContext();
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('http://localhost/', 'get');
        $dispatcher = new EventDispatcher();

        $listener = new LocaleListener('de', $this->requestStack, $requestContext);

        $dispatcher->addSubscriber($listener);

        $getResponseEvent = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $dispatcher->dispatch(KernelEvents::REQUEST, $getResponseEvent);

        $finishRequestEvent = new FinishRequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $dispatcher->dispatch(KernelEvents::FINISH_REQUEST, $finishRequestEvent);

        $this->assertEquals('de', $requestContext->getParameter('_locale'));

    }
}

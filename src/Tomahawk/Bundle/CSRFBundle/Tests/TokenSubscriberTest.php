<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManager;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Bundle\CSRFBundle\Event\TokenSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TokenSubscriberTest extends TestCase
{
    public function testTokenSubscriberHandlesCorrectEvents()
    {
        $tokenManager = $this->getTokenManager();

        $tokenSubscriber = new TokenSubscriber($tokenManager);

        $this->assertEquals(array(
            KernelEvents::REQUEST => 'onRequest',
        ), $tokenSubscriber->getSubscribedEvents());
    }

    public function testTokenSubscriberExitsWhenProtectionIsNotRequired()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(false, $tokenManager->getTokenName());
        $event = $this->getEvent($request);

        $tokenSubscriber = new TokenSubscriber($tokenManager);
        $tokenSubscriber->onRequest($event);
    }

    /**
     * @expectedException \Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException
     */
    public function testTokenSubscriberThrowsExceptionOnNoToken()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(true, $tokenManager->getTokenName());
        $event = $this->getEvent($request);

        $tokenSubscriber = new TokenSubscriber($tokenManager);
        $tokenSubscriber->onRequest($event);
    }

    /**
     * @expectedException \Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException
     */
    public function testTokenSubscriberThrowsExceptionOnInvalidToken()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(true, $tokenManager->getTokenName(), 'atokffen');
        $event = $this->getEvent($request);

        $tokenSubscriber = new TokenSubscriber($tokenManager);
        $tokenSubscriber->onRequest($event);
    }

    protected function getSession()
    {
        $session = $this->getMock('Tomahawk\Session\SessionInterface');
        return $session;
    }

    protected function getContainer()
    {
        return $this->getMock('Tomahawk\DI\ContainerInterface');
    }

    protected function getTokenManager()
    {
        $mock = $this->getMock('Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface');

        $mock->expects($this->any())
            ->method('getTokenName')
            ->will($this->returnValue('_token'));

        $mock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue('atoken'));

        return $mock;
    }

    protected function getRequest($filterCsrf = true, $tokenName, $token = null)
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'get',
                'getMethod',
            ))
            ->getMock();

        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('filter_csrf', null, false, $filterCsrf),
                array($tokenName, null, false, $token),
            )));

        $mock->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('POST'));

        return $mock;
    }

    protected function getEvent($request = null)
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(array(
               'getRequest',
            ))
            ->getMock();

        $mock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $mock;
    }
}

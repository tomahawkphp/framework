<?php

namespace Tomahawk\Security\Csrf\Test;

use Symfony\Component\HttpKernel\KernelEvents;
use PHPUnit\Framework\TestCase;
use Tomahawk\Security\Csrf\EventListener\TokenListener;

class TokenListenerTest extends TestCase
{
    public function testTokenListenerHandlesCorrectEvents()
    {
        $tokenManager = $this->getTokenManager();

        $TokenListener = new TokenListener($tokenManager);

        $this->assertEquals(array(
            KernelEvents::REQUEST => 'onRequest',
        ), $TokenListener->getSubscribedEvents());
    }

    /**
     * @throws \Tomahawk\Security\Csrf\Exception\InvalidTokenException
     * @throws \Tomahawk\Security\Csrf\Exception\TokenNotFoundException
     */
    public function testTokenListenerExitsWhenProtectionIsNotRequired()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(false, $tokenManager->getTokenName());
        $event = $this->getEvent($request);

        $TokenListener = new TokenListener($tokenManager);
        $TokenListener->onRequest($event);
    }

    /**
     * @throws \Tomahawk\Security\Csrf\Exception\InvalidTokenException
     * @throws \Tomahawk\Security\Csrf\Exception\TokenNotFoundException
     */
    public function testTokenListenerExitsWhenProtectionIsNotRequiredAndFilterIsTrue()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(true, $tokenManager->getTokenName(), null, 'GET');
        $event = $this->getEvent($request);

        $TokenListener = new TokenListener($tokenManager);
        $TokenListener->onRequest($event);
    }

    /**
     * @expectedException \Tomahawk\Security\Csrf\Exception\TokenNotFoundException
     */
    public function testTokenListenerThrowsExceptionOnNoToken()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(true, $tokenManager->getTokenName());
        $event = $this->getEvent($request);

        $TokenListener = new TokenListener($tokenManager);
        $TokenListener->onRequest($event);
    }

    /**
     * @expectedException \Tomahawk\Security\Csrf\Exception\InvalidTokenException
     */
    public function testTokenListenerThrowsExceptionOnInvalidToken()
    {
        $tokenManager = $this->getTokenManager();

        $request = $this->getRequest(true, $tokenManager->getTokenName(), 'atokffen');
        $event = $this->getEvent($request);

        $TokenListener = new TokenListener($tokenManager);
        $TokenListener->onRequest($event);
    }

    protected function getSession()
    {
        $session = $this->getMockBuilder('Tomahawk\Session\SessionInterface')->getMock();
        return $session;
    }

    protected function getContainer()
    {
        return $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
    }

    protected function getTokenManager()
    {
        $mock = $this->getMockBuilder('Tomahawk\Security\Csrf\Token\TokenManagerInterface')->getMock();

        $mock->expects($this->any())
            ->method('getTokenName')
            ->will($this->returnValue('_token'));

        $mock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue('atoken'));

        return $mock;
    }

    protected function getRequest($filterCsrf = true, $tokenName, $token = null, $method = 'POST')
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
                array('filter_csrf', null, $filterCsrf),
                array($tokenName, null, $token),
            )));

        $mock->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method));

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
<?php

namespace Tomahawk\Bundle\CSRFBundle\Test;

use Symfony\Component\HttpKernel\KernelEvents;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\CSRFBundle\EventListener\TokenListener;

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
     * @throws \Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException
     * @throws \Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException
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
     * @throws \Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException
     * @throws \Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException
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
     * @expectedException \Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException
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
     * @expectedException \Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException
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
        $session = $this->getMock('Tomahawk\Session\SessionInterface');
        return $session;
    }

    protected function getContainer()
    {
        return $this->getMock('Tomahawk\DependencyInjection\ContainerInterface');
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

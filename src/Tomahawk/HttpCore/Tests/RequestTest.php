<?php

namespace Tomahawk\HttpCore\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\HttpCore\Request;
use Tomahawk\Session\SessionInterface;
use Tomahawk\Session\Bag\OldInputBagInterface;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $oldInputBag = $this->getOldInputBag();

        $oldInputBag->expects($this->once())
            ->method('get')
            ->will($this->returnValue('Tom'));


        $session = $this->getSession();
        $session->expects($this->once())
            ->method('getOldInputBag')
            ->will($this->returnValue($oldInputBag));

        $request = new Request();
        $request->setSession($session);


        $this->assertEquals('Tom', $request->getOld('name'));
    }

    protected function getSession()
    {
        return $this->getMock(SessionInterface::class);
    }

    protected function getOldInputBag()
    {
        return $this->getMock(OldInputBagInterface::class);
    }
}

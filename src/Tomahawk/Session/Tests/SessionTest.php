<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Session\Session;
use Tomahawk\Session\Bag\OldInputBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    protected $session;

    public function setup()
    {
        $storage = new MockArraySessionStorage();
        $oldInputBag = $this->getOldInputBag();
        $this->session = new Session($storage, null, null, $oldInputBag);
    }

    public function tearDown()
    {
        $this->session = null;
    }

    public function testSession()
    {
        $this->assertInstanceOf(OldInputBagInterface::class, $this->session->getOldInputBag());
    }

    protected function getOldInputBag()
    {
        return $this->getMock(OldInputBagInterface::class);
    }

}

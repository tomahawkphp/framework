<?php

namespace Tomahawk\Authentication\Tests\Storage;

use Tomahawk\Test\TestCase;
use Tomahawk\Authentication\Storage\SessionStorage;

class SessionStorageTest extends TestCase
{
    const STORAGE_KEY = '__th_session';

    const IDENTIFIER = 1;

    public function testStorage()
    {
        $session = $this->getSession();

        $session->expects($this->once())
            ->method('set');

        $session->expects($this->once())
            ->method('get')
            ->will($this->returnValue(self::IDENTIFIER));

        $session->expects($this->once())
            ->method('remove');

        $storage = new SessionStorage($session);

        $storage->setIdentifier(self::STORAGE_KEY, self::IDENTIFIER);
        $this->assertEquals(self::IDENTIFIER, $storage->getIdentifier(self::STORAGE_KEY));
        $storage->removeIdentifier(self::IDENTIFIER);
    }

    protected function getSession()
    {
        return $this->getMock('Tomahawk\Session\SessionInterface');

        $builder = $this->getMockBuilder('Tomahawk\Session\SessionInterface');

        $builder->setMethods(array(
            'set',
            'get',
            'remove',
        ));

        return $builder->getMock();
    }
}

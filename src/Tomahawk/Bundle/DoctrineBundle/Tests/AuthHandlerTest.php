<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\Auth\Handlers\DoctrineAuthHandler;

class AuthHandlerTest extends TestCase
{
    public function testThing()
    {

    }

    /*public function testRetrieveByIDReturnsNull()
    {
        $registry = $this->getRegistry();
        $repo = $this->getRepo();

        $repo->expects($this->once())
            ->method('find')
            ->will($this->returnValue(null));

        $registry->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $handler = new DoctrineAuthHandler($this->getHasher(), $registry, 'Model', 'username');

        $this->assertEquals(null, $handler->retrieveById(1));
    }

    public function testRetrieveByIDReturnsUser()
    {
        $registry = $this->getRegistry();
        $repo = $this->getRepo();

        $repo->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->getUser()));

        $registry->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $handler = new DoctrineAuthHandler($this->getHasher(), $registry, 'Model', 'username');

        $this->assertInstanceOf('Tomahawk\Auth\UserInterface', $handler->retrieveById(1));
    }

    public function testRetrieveByCredentials()
    {
        $registry = $this->getRegistry();
        $repo = $this->getRepo();

        $repo->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->getUser()));

        $registry->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $handler = new DoctrineAuthHandler($this->getHasher(), $registry, 'Model', 'username');

        $this->assertInstanceOf('Tomahawk\Auth\UserInterface', $handler->retrieveByCredentials(array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    public function testValidateCredentials()
    {
        $registry = $this->getRegistry();

        $user = $this->getUser();

        $user->expects($this->once())
            ->method('getAuthPassword')
            ->will($this->returnValue('aaaaaaaaaaa'));

        $hasher = $this->getHasher();

        $hasher->expects($this->once())
            ->method('check')
            ->will($this->returnValue(true));

        $handler = new DoctrineAuthHandler($hasher, $registry, 'Model', 'username');

        $this->assertTrue($handler->validateCredentials($user, array(
            'username' => 'tomgrohl',
            'password' => 'password'
        )));
    }

    protected function getRegistry()
    {
        return $this->getMock('Tomahawk\Bundle\DoctrineBundle\RegistryInterface');
    }

    protected function getRepo()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
    }

    protected function getUser()
    {
        return $this->getMock('Tomahawk\Auth\UserInterface');
    }

    protected function getHasher()
    {
        return $this->getMock('Tomahawk\Hashing\HasherInterface');
    }*/
}

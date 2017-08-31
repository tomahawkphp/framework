<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Authentication\User;

use PHPUnit\Framework\TestCase;
use Tomahawk\Bundle\DoctrineBundle\Authentication\User\DoctrineUserProvider;

class DoctrineUserProviderTest extends TestCase
{
    public function testProvider()
    {
        $registry = $this->getRegistry();

        $repo = $this->getEntityRepo();

        $repo->expects($this->atLeast(1))
            ->method('findOneBy')
            ->will($this->returnValue($this->getMockBuilder('Tomahawk\Authentication\User\UserInterface')->getMock()));

        $registry->expects($this->atLeast(1))
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $provider = new DoctrineUserProvider($registry, 'User', 'username');

        $this->assertInstanceOf('Tomahawk\Authentication\User\UserInterface', $provider->findUserByUsername('tommy'));
    }

    protected function getEntityRepo()
    {
        return $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')->getMock();
    }

    protected function getRegistry()
    {
        return $this->getMockBuilder('Tomahawk\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

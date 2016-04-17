<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Auth\User;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\DoctrineBundle\Auth\User\DoctrineUserProvider;

class DoctrineUserProviderTest extends TestCase
{
    public function testProvider()
    {
        $registry = $this->getRegistry();

        $repo = $this->getEntityRepo();

        $repo->expects($this->atLeast(1))
            ->method('findOneBy')
            ->will($this->returnValue($this->getMock('Tomahawk\Auth\User\UserInterface')));

        $registry->expects($this->atLeast(1))
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $provider = new DoctrineUserProvider($registry, 'User', 'username');

        $this->assertInstanceOf('Tomahawk\Auth\User\UserInterface', $provider->findUserByUsername('tommy'));
    }

    protected function getEntityRepo()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
    }

    protected function getRegistry()
    {
        return $this->getMockBuilder('Tomahawk\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getConfig($defaultProvider = 'memory')
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap([
                ['security.providers.memory', null, [
                        'users' => $this->users,
                    ]
                ]
            ]));

        return $config;
    }
}

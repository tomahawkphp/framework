<?php

namespace Tomahawk\Authentication\Tests\User;

use Tomahawk\Authentication\User\User;
use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\User\InMemoryUserProvider;

class InMemoryUserProviderTest extends TestCase
{
    /**
     * @var array
     */
    private $users = array(
        'tommy' => array(
            // password is mypasswordbaby
            'password' => '$2y$10$A21VGSNYOFlNZaavgTs52.Y2cKnxmAf6KfL/RiVNsHE1TGT3ZGTwC',
        ),
    );

    public function testUserProvider()
    {
        $userProvider = $this->getUserProvider();

        $user = $userProvider->findUserByUsername('tommy');

        $this->assertInstanceOf('\Tomahawk\Authentication\User\UserInterface', $user);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage User has already been added
     */
    public function testUserProviderCreateExistingUser()
    {
        $userProvider = $this->getUserProvider();

        $userProvider->createUser(new User('tommy', 'pass'));
    }

    public function testUserProviderCreateNoneExistingUser()
    {
        $userProvider = $this->getUserProvider();

        $userProvider->createUser(new User('fred', '$2y$10$A21VGSNYOFlNZaavgTs52.Y2cKnxmAf6KfL/RiVNsHE1TGT3ZGTwC'));

        $user = $userProvider->findUserByUsername('fred');

        $this->assertInstanceOf('\Tomahawk\Authentication\User\UserInterface', $user);
    }

    public function testUserProviderWithInvalidUsername()
    {
        $userProvider = $this->getUserProvider();

        $user = $userProvider->findUserByUsername('none');

        $this->assertNull($user);
    }

    private function getUserProvider()
    {
        return new InMemoryUserProvider($this->users);
    }
}

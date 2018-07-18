<?php

namespace Tomahawk\Authentication\Tests\User;

use PHPUnit\Framework\TestCase;
use Tomahawk\Authentication\User\User;

class UserTest extends TestCase
{
    const USERNAME = 'tommy';

    const PASSWORD = 'mypasswordbaby';

    public function testUser()
    {
        $user = new User(self::USERNAME, self::PASSWORD);

        $this->assertEquals(self::USERNAME, $user->getUsername());
        $this->assertEquals(self::PASSWORD, $user->getPassword());
    }
}

<?php

namespace Tomahawk\Auth\Tests\User;

use Tomahawk\Test\TestCase;
use Tomahawk\Auth\User\Credentials;

class CredentialsTest extends TestCase
{
    const USERNAME = 'tommy';

    const PASSWORD = 'mypasswordbaby';

    public function testCredentials()
    {
        $credentials = new Credentials(
            self::USERNAME,
            self::PASSWORD
        );

        $this->assertEquals(self::USERNAME, $credentials->getUsername());
        $this->assertEquals(self::PASSWORD, $credentials->getPassword());
    }
}

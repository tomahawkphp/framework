<?php

namespace Tomahawk\Authentication\Tests\User;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Authentication\User\Credentials;

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

<?php

namespace Tomahawk\Authentication\Tests\Encoder;

use Tomahawk\Test\TestCase;
use Tomahawk\Authentication\Encoder\BCryptPasswordEncoder;

class BCryptPasswordEncoderTest extends TestCase
{
    const PASSWORD = 'password123';
    const VALID_COST = 4;

    public function testResultLength()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $result = $encoder->encodePassword(self::PASSWORD, null);

        $this->assertEquals(60, strlen($result));
    }

    public function testValidation()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $result = $encoder->encodePassword(self::PASSWORD, null);

        $this->assertTrue($encoder->isPasswordValid($result, self::PASSWORD, null));
        $this->assertFalse($encoder->isPasswordValid($result, 'wrongPassword', null));
    }

    /**
     * @expectedException \Tomahawk\Authentication\Exception\BadCredentialsException
     */
    public function testLongPassword()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $encoder->encodePassword(str_repeat('a', '73'), null);
    }

    public function testCheckPasswordLength()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $result = $encoder->encodePassword(str_repeat('a', '72'), null);

        $this->assertTrue($encoder->isPasswordValid($result, str_repeat('a', '72'), null));
        $this->assertFalse($encoder->isPasswordValid($result, str_repeat('a', '73'), null));
    }
}

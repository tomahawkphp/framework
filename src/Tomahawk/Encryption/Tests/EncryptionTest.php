<?php

namespace Tomahawk\Encryption\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Encryption\Crypt;

class EncyrptionTest extends TestCase
{
    /**
     * @var Crypt
     */
    protected $crypt;

    public function setUp()
    {
        $key = str_repeat('ab', 16);
        $this->crypt = new Crypt($key);
        parent::setUp();
    }

    public function testValidEncyrption()
    {
        $encrypted_string = $this->crypt->encrypt('tom');

        $this->assertEquals('tom', $this->crypt->decrypt($encrypted_string));
    }

    public function testInvalidEncyrption()
    {
        $encrypted_string = 'xI2VRcEmm8Pz0GqSZQF8ZrhIUNsR9GDvEBTyZo5tdghnbUtSREtzTEFzT25EaWo1WmxEY1NyR3lqNjVnOGUyRkllc';

        $this->assertFalse($this->crypt->decrypt($encrypted_string));
    }

    public function testChangeBlockLength()
    {
        $this->crypt->setBlockLength(196);

        $this->assertEquals(196, $this->crypt->getBlockLength());

        $encrypted_string = $this->crypt->encrypt('tom');

        $this->assertEquals('tom', $this->crypt->decrypt($encrypted_string));
    }

    public function testChangeMode()
    {
        $this->crypt->setMode(MCRYPT_MODE_ECB);

        $this->assertEquals(MCRYPT_MODE_ECB, $this->crypt->getMode());

        $encrypted_string = $this->crypt->encrypt('tom');

        $this->assertEquals('tom', $this->crypt->decrypt($encrypted_string));
    }

}

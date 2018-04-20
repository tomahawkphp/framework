<?php

namespace Tomahawk\Hashing\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Hashing\ArgonHasher;
use Tomahawk\Hashing\Hasher;
use Tomahawk\Hashing\Test\Hasher as TestHasher;

class HashingTest extends TestCase
{
    public function testBcryptHashing()
    {
        $hasher = new Hasher();

        $value = $hasher->make('hashmebitch');
        $this->assertTrue($value !== 'hashmebitch');
        $this->assertTrue($hasher->check('hashmebitch', $value));
        $this->assertTrue(!$hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, array('rounds' => 2)));
    }

    public function testBasicArgonHashing()
    {
        if (! defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('PHP not compiled with argon2 hashing support.');
        }
        $hasher = new ArgonHasher();
        $value = $hasher->make('password');
        $this->assertNotSame('password', $value);
        $this->assertTrue($hasher->check('password', $value));
        $this->assertFalse($hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, ['threads' => 1]));
    }

    /**
     * @expectedException \RunTimeException
     */
    public function testException()
    {
        $hasher = new TestHasher();
        $value = $hasher->make('hashmebitch');
    }
}

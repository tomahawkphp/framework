<?php

namespace Tomahawk\Hashing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Hashing\Hasher;
use Tomahawk\Hashing\Test\Hasher as TestHasher;

class HashingTest extends TestCase
{
    public function testHash()
    {
        $hasher = new Hasher();

        $value = $hasher->make('hashmebitch');
        $this->assertTrue($value !== 'hashmebitch');
        $this->assertTrue($hasher->check('hashmebitch', $value));
        $this->assertTrue(!$hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, array('rounds' => 2)));
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

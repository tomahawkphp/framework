<?php

use Tomahawk\Hashing\Hasher;

class HashingTest extends PHPUnit_Framework_TestCase
{
    public function testThing()
    {
        $hasher = new Hasher();

        $value = $hasher->make('hashmebitch');
        $this->assertTrue($value !== 'hashmebitch');
        $this->assertTrue($hasher->check('hashmebitch', $value));
        $this->assertTrue(!$hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, array('rounds' => 2)));
    }
}
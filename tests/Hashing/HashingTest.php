<?php

use Tomahawk\Hashing\Hasher;

class HashingTest extends PHPUnit_Framework_TestCase
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

    public function testException()
    {
        $this->setExpectedException('RuntimeException');
        $hasher = new HasherStub();
        $value = $hasher->make('hashmebitch');
    }
}


class HasherStub extends Hasher
{
    protected function doHash($value, $algo, array $options = array())
    {
        return false;
    }
}
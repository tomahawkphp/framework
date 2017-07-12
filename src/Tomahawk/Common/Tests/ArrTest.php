<?php

namespace Tomahawk\Common\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Common\Arr;

class ArrTest extends TestCase
{
    public function testFirstReturnsCorrectValue()
    {
        $this->assertEquals(3, Arr::first(array(3,2,1)));
    }

    public function testFirstByReturnsCorrectValue()
    {
        $this->assertEquals(2, Arr::firstBy(array(3,2,1), function($key, $value) {
            return $value === 2;
        }));

        $this->assertEquals('a', Arr::firstBy(array('a','b','c'), function($key, $value) {
            return $value === 'a';
        }));

        $this->assertEquals(null, Arr::firstBy(array('a','b','c'), function($key, $value) {
            return $value === 'd';
        }));
    }

    public function testLastReturnsCorrectValue()
    {
        $this->assertEquals(1, Arr::last(array(3,2,1)));
    }

    public function testOnly()
    {
        $array = array(
            'foo' => 'bar',
            'baz' => 'boom'
        );

        $this->assertEquals(array('foo' => 'bar'), Arr::only($array, 'foo'));
    }

    public function testExcept()
    {
        $array = array(
            'foo' => 'bar',
            'baz' => 'boom'
        );

        $this->assertEquals(array('baz' => 'boom'), Arr::except($array, 'foo'));
    }

    public function testArrayGetReturnsValue()
    {
        $array = array(
            'foo' => 'bar',
            'baz' => 'boom'
        );

        $this->assertEquals('bar', Arr::get($array, 'foo'));
    }

    public function testArrayGetReturnsDefaultValue()
    {
        $array = array(
            'foo' => 'bar',
        );

        $this->assertEquals('boom', Arr::get($array, 'baz', 'boom'));
    }

    public function testArraySet()
    {
        $array = array(
            'foo' => 'bar',
        );

        Arr::set($array, 'baz', 'boom');

        $this->assertEquals('boom', Arr::get($array, 'baz'));
    }

    public function testArrayHas()
    {
        $array = array(
            'foo' => 'bar',
        );

        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertFalse(Arr::has($array, 'bar'));
    }

    public function testArrayContains()
    {
        $array = array(
            'foo' => 'bar',
        );

        $this->assertTrue(Arr::contains($array, 'bar'));
        $this->assertFalse(Arr::contains($array, 'boom'));
    }

}

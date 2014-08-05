<?php

namespace Tomahawk\Common\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Common\Arr;

class ArrTest extends TestCase
{
    public function testPluckArray()
    {
        $people = array(
            array(
                'name' => 'Tom',
                'age'  => 27
            ),
            array(
                'name' => 'Melia',
                'age'  => 25
            )
        );

        $this->assertEquals(array('Tom', 'Melia'), Arr::pluck($people, 'name'));
    }

    public function testPluckObject()
    {
        $people = array();

        $person1 = new \stdClass();
        $person1->name = 'Tom';
        $person1->age = 27;

        $person2 = new \stdClass();
        $person2->name = 'Melia';
        $person2->age = 25;


        array_push($people, $person1, $person2);

        $this->assertEquals(array('Tom', 'Melia'), Arr::pluck($people, 'name'));
    }

    public function testOnly()
    {
        $array = array(
            'foo' => 'bar',
            'baz' => 'boom'
        );

        //var_dump(Arr::only($array, 'foo'));

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

}
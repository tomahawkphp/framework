<?php
namespace Tomahawk\Common\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Common\Str;

class StrTest extends TestCase
{
    public function testStartsWithTrue()
    {
        $string = 'cheesemuffin';

        $this->assertTrue(Str::startsWith($string, 'cheese'));
    }

    public function testStartsWithFalse()
    {
        $string = 'cheesemuffin';

        $this->assertFalse(Str::startsWith($string, 'foo'));
    }


    public function testEndsWithTrue()
    {
        $string = 'cheesemuffin';

        $this->assertTrue(Str::endsWith($string, 'muffin'));
    }

    public function testEndsWithFalse()
    {
        $string = 'cheesemuffin';

        $this->assertFalse(Str::endsWith($string, 'foo'));
    }

    public function testStartsWithMultipleTrue()
    {
        $string = 'cheesemuffin';

        $this->assertTrue(Str::startsWith($string, array('cheese', 'olive')));
    }

    public function testStrConvertstoLowercase()
    {
        $string = 'TOM';

        $this->assertEquals('tom', Str::lower($string));
    }

    public function testStrConvertstoUppercase()
    {
        $string = 'tom';

        $this->assertEquals('TOM', Str::upper($string));
    }

    public function testStrConvertstoTitlecase()
    {
        $string = 'tom';

        $this->assertEquals('Tom', Str::title($string));
    }
}
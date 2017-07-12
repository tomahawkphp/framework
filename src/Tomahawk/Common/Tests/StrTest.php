<?php
namespace Tomahawk\Common\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Common\Str;
use Tomahawk\Common\Test\Str as TestStr;
use Tomahawk\Common\Test\OpenStr as TestOpenStr;

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

    public function testStrRandomReturnsCorrectLength()
    {
        $this->assertEquals(10, strlen(Str::random(10)));
    }

    public function testStrQuickRandomReturnsCorrectLength()
    {
        $this->assertEquals(10, strlen(Str::quickRandom(10)));
    }

    public function testCamelCase()
    {
        $this->assertEquals('tomEllis', Str::camelCase('tom_ellis'));
    }

    public function testStudlyCase()
    {
        $this->assertEquals('TomEllis', Str::studlyCase('tom_ellis'));
    }

    public function testSlug()
    {
        $this->assertEquals('tom-ellis', Str::slug('tom_ellis'));
    }

    public function testIs()
    {
        $this->assertTrue(Str::is('tom', 'tom'));
        $this->assertTrue(Str::is('account/dashboard', 'account/*'));
    }

    public function testRandomWhenOpenSSLIsNotAvailable()
    {
        $this->assertEquals(10, strlen(TestStr::random(10)));
    }

    public function testRandomWhenOpenSSLIsAvailableButInvalid()
    {
        $this->assertEquals(false, strlen(TestOpenStr::random(10)));
    }
}

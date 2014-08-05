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

    public function testStartsWithMultipleTrue()
    {
        $string = 'cheesemuffin';

        $this->assertTrue(Str::startsWith($string, array('cheese', 'olive')));
    }
}
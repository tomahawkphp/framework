<?php

namespace Tomahawk\HttpCore\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\Response\Cookies;
use Symfony\Component\HttpFoundation\Request;

class CookiesTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    protected $key;

    public function setUp()
    {
        $this->key = str_repeat('a', 32);
        $this->request = Request::createFromGlobals();
        parent::setUp();
    }

    public function testSet()
    {
        $cookies = new Cookies($this->request, $this->key);
        $cookies->set('name', 'Tom');

        $this->assertCount(1, $cookies->getQueued());
        $this->assertFalse($cookies->has('name'));
    }

    public function testHas()
    {
        $value = hash_hmac('sha1', 'Tom', $this->key) .'+Tom';

        $request = new Request(array(), array(), array(), array('name' => $value));
        $cookies = new Cookies($request, $this->key);

        $this->assertTrue($cookies->has('name'));
        $this->assertEquals('Tom', $cookies->get('name'));
    }

    public function testHasFailsWithInvalidCookie()
    {
        $value = hash_hmac('sha1', 'Tom', $this->key);

        $request = new Request(array(), array(), array(), array('name' => $value));
        $cookies = new Cookies($request, $this->key);

        $this->assertTrue($cookies->has('name'));
        $this->assertEquals(null, $cookies->get('name'));
    }

    public function testHasNotExists()
    {
        $request = new Request();
        $cookies = new Cookies($request, $this->key);

        $this->assertFalse($cookies->has('name'));
        $this->assertEquals(null, $cookies->get('name'));
        $this->assertEquals('default', $cookies->get('name', 'default'));
    }

    public function testExpire()
    {
        $request = new Request(array(), array(), array(), array('name' => 'Tom'));
        $cookies = new Cookies($request, $this->key);

        $this->assertTrue($cookies->has('name'));
        $cookies->expire('name');
        $this->assertFalse($cookies->has('name'));
    }

}

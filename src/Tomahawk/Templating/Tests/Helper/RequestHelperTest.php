<?php

namespace Tomahawk\Routing\Tests;

use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Tomahawk\Templating\Helper\RequestHelper;

class RequestHelperTest extends TestCase
{
    protected $requestStack;
    protected function setUp()
    {
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->initialize(array('foobar' => 'bar'));
        $this->requestStack->push($request);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetParameterThrowsExceptionOnNoRequest()
    {
        $helper = new RequestHelper(new RequestStack());
        $this->assertNull($helper->getParameter('foo'));
    }

    public function testGetParameter()
    {
        $helper = new RequestHelper($this->requestStack);
        $this->assertEquals('bar', $helper->getParameter('foobar'));
        $this->assertEquals('foo', $helper->getParameter('bar', 'foo'));
        $this->assertNull($helper->getParameter('foo'));
    }

    public function testGetLocale()
    {
        $helper = new RequestHelper($this->requestStack);
        $this->assertEquals('en', $helper->getLocale());
    }

    public function testGetName()
    {
        $helper = new RequestHelper($this->requestStack);
        $this->assertEquals('request', $helper->getName());
    }
}

<?php

namespace Tomahawk\HttpCore\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\ResponseBuilder;

class ResponseTest extends TestCase
{
    /**
     * @var ResponseBuilder
     */
    protected $response;

    public function setUp()
    {
        $this->response = new ResponseBuilder();
        parent::setUp();
    }

    public function testContent()
    {
        $response = $this->response->content('Im content');

        $this->assertEquals('Im content', $response->getContent());
    }

    public function testRedirect()
    {
        $response = $this->response->redirect('http://google.com');

        $this->assertEquals(1, preg_match(
            '#<meta http-equiv="refresh" content="\d+;url=http://google\.com" />#',
            preg_replace(array('/\s+/', '/\'/'), array(' ', '"'), $response->getContent())
        ));
    }

    public function testStream()
    {
        $response = $this->response->stream(function () { echo 'foo'; }, 404, array('Content-Type' => 'text/plain'));

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->headers->get('Content-Type'));
    }

    public function testJson()
    {
        $response = $this->response->json(array(0, 1, 2, 3));
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function testDownload()
    {
        $response =  $this->response->download(__FILE__);
        $this->assertFalse($response->getContent());
    }
}
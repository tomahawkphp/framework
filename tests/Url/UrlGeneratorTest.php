<?php

use Tomahawk\Url\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    public function setup()
    {
        $this->urlGenerator = new UrlGenerator(new RouteCollection(), new RequestContext());
    }

    public function testThing()
    {
        $url = $this->urlGenerator->to('http://google.com');

        $this->assertEquals('http://google.com', $url);
    }
}
<?php

namespace Tomahawk\Url\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Url\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class UrlGeneratorTest extends TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    public function setup()
    {
        $this->request = Request::create('http://symfony.devbox.com:8182/', 'GET');
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        $this->url = new UrlGenerator(new RouteCollection(), $this->context);
    }

    public function testValidUrl()
    {
        $url = $this->url->to('#home');
        $this->assertEquals('#home', $url);

        $url = $this->url->to('//google.com');
        $this->assertEquals('//google.com', $url);

        $url = $this->url->to('mailto: person@example.com');
        $this->assertEquals('mailto: person@example.com', $url);

        $url = $this->url->to('tel:012345667788');
        $this->assertEquals('tel:012345667788', $url);
    }

    public function testExternalUrl()
    {
        $url = $this->url->to('http://google.com');
        $this->assertEquals('http://google.com', $url);
    }

    public function testGetBaseURL()
    {
        $this->assertEquals('', $this->url->getBaseUrl());
        $this->assertEquals('http://symfony.devbox.com:8182', $this->url->getCurrentUrl());
    }

    public function testSSLOff()
    {
        $this->url->setSslOn(false);
        $this->assertFalse($this->url->getSslOn());
        $this->assertEquals('http://symfony.devbox.com:8182/users', $this->url->to('users'));
        $this->assertEquals('http://symfony.devbox.com:8182/users', $this->url->secureTo('users'));
    }

    public function testSSLOn()
    {
        $this->url->setSslOn(true);

        $this->assertTrue($this->url->getSslOn());
        $this->assertEquals('http://symfony.devbox.com:8182/users', $this->url->to('users'));
        $this->assertEquals('https://symfony.devbox.com/users', $this->url->secureTo('users'));
    }

    public function testSSLOnDifferentPort()
    {
        $this->request = Request::create('http://symfony.devbox.com:8182/', 'GET');
        $this->context = new RequestContext();
        $this->context->setHttpsPort(456);
        $this->context->fromRequest($this->request);
        $this->url = new UrlGenerator(new RouteCollection(), $this->context);
        $this->url->setSslOn(true);

        $this->assertEquals('https://symfony.devbox.com:456/users', $this->url->secureTo('users'));
    }

}

<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Templating\Helper\UrlHelper;
use Tomahawk\Url\UrlGeneratorInterface;

class UrlHelperTest extends TestCase
{
    public function testHelperReturnsCorrectName()
    {
        $helper = new UrlHelper($this->getUrlGenerator());
        $this->assertEquals('url', $helper->getName());
    }

    public function testHelperCallsUrlToMethod()
    {
        $urlGenerator = $this->getUrlGenerator();

        $urlGenerator->expects($this->once())
            ->method('to');

        $helper = new UrlHelper($urlGenerator);
        $helper->to('/');
    }

    public function testHelperCallsUrlRouteMethod()
    {
        $urlGenerator = $this->getUrlGenerator();

        $urlGenerator->expects($this->once())
            ->method('route');

        $helper = new UrlHelper($urlGenerator);
        $helper->route('home');
    }

    protected function getUrlGenerator()
    {
        return $this->getMock('Tomahawk\Url\UrlGeneratorInterface');
    }
}

<?php

namespace Tomahawk\Templating\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Tomahawk\Templating\Twig\Extension\UrlExtension;

class UrlExtensionTest extends TestCase
{
    public function testCorrectNumberOfFunctionsAreReturned()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertCount(6, $urlExtension->getFunctions());
    }

    public function testExtensionNameIsReturned()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('url', $urlExtension->getName());
    }

    public function testRouteFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('/account', $urlExtension->route('account'));
    }

    public function testAssetFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('css/style.css', $urlExtension->asset('css/style.css'));
    }

    public function testToFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('http://example.com', $urlExtension->to('/'));
    }

    public function testToSecureFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('https://example.com', $urlExtension->secureTo('/'));
    }

    public function testBaseUrlFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('/', $urlExtension->getBaseUrl());
    }

    public function testCurrentUrlFunction()
    {
        $urlExtension = new UrlExtension($this->getUrlGenerator());

        $this->assertEquals('/user', $urlExtension->getCurrentUrl());
    }

    protected function getUrlGenerator()
    {
        $urlGenerator = $this->createMock('Tomahawk\Url\UrlGeneratorInterface');

        $urlGenerator->expects($this->any())
            ->method('route')
            ->will($this->returnValue('/account'));

        $urlGenerator->expects($this->any())
            ->method('asset')
            ->will($this->returnValue('css/style.css'));

        $urlGenerator->expects($this->any())
            ->method('to')
            ->will($this->returnValue('http://example.com'));

        $urlGenerator->expects($this->any())
            ->method('secureTo')
            ->will($this->returnValue('https://example.com'));

        $urlGenerator->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('/'));

        $urlGenerator->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue('/user'));

        return $urlGenerator;
    }
}

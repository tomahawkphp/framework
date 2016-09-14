<?php

namespace Tomahawk\Routing\Tests;

use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Test\TestCase;
use Tomahawk\HttpCore\Request;
use Tomahawk\Routing\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RedirectControllerTest extends TestCase
{
    public function testEmptyPath()
    {
        $request = new Request();
        $controller = $this->createRedirectController();

        try {
            $controller->urlRedirectAction($request, '', true);
            $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException to be thrown');
        } catch (HttpException $e) {
            $this->assertSame(410, $e->getStatusCode());
        }
        try {
            $controller->urlRedirectAction($request, '', false);
            $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException to be thrown');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function testFullURL()
    {
        $request = new Request();
        $controller = $this->createRedirectController();
        $returnResponse = $controller->urlRedirectAction($request, 'http://foo.bar/');
        $this->assertRedirectUrl($returnResponse, 'http://foo.bar/');
        $this->assertEquals(302, $returnResponse->getStatusCode());
    }

    public function testUrlRedirectDefaultPortParameters()
    {
        $host = 'www.example.com';
        $baseUrl = '/base';
        $path = '/redirect-path';
        $httpPort = 1080;
        $httpsPort = 1443;
        $expectedUrl = "https://$host:$httpsPort$baseUrl$path";
        $request = $this->createRequestObject('http', $host, $httpPort, $baseUrl);
        $controller = $this->createRedirectController(null, $httpsPort);
        $returnValue = $controller->urlRedirectAction($request, $path, false, 'https');
        $this->assertRedirectUrl($returnValue, $expectedUrl);
        $expectedUrl = "http://$host:$httpPort$baseUrl$path";
        $request = $this->createRequestObject('https', $host, $httpPort, $baseUrl);
        $controller = $this->createRedirectController($httpPort);
        $returnValue = $controller->urlRedirectAction($request, $path, false, 'http');
        $this->assertRedirectUrl($returnValue, $expectedUrl);
    }

    public function urlRedirectProvider()
    {
        return array(
            // Standard ports
            array('http',  null, null,  'http',  80,   ''),
            array('http',  80,   null,  'http',  80,   ''),
            array('https', null, null,  'http',  80,   ''),
            array('https', 80,   null,  'http',  80,   ''),
            array('http',  null,  null, 'https', 443,  ''),
            array('http',  null,  443,  'https', 443,  ''),
            array('https', null,  null, 'https', 443,  ''),
            array('https', null,  443,  'https', 443,  ''),
            // Non-standard ports
            array('http',  null,  null, 'http',  8080, ':8080'),
            array('http',  4080,  null, 'http',  8080, ':4080'),
            array('http',  80,    null, 'http',  8080, ''),
            array('https', null,  null, 'http',  8080, ''),
            array('https', null,  8443, 'http',  8080, ':8443'),
            array('https', null,  443,  'http',  8080, ''),
            array('https', null,  null, 'https', 8443, ':8443'),
            array('https', null,  4443, 'https', 8443, ':4443'),
            array('https', null,  443,  'https', 8443, ''),
            array('http',  null,  null, 'https', 8443, ''),
            array('http',  8080,  4443, 'https', 8443, ':8080'),
            array('http',  80,    4443, 'https', 8443, ''),
            array('http',  80,    4443, null, 80, ''),
        );
    }
    /**
     * @dataProvider urlRedirectProvider
     */
    public function testUrlRedirect($scheme, $httpPort, $httpsPort, $requestScheme, $requestPort, $expectedPort)
    {
        $host = 'www.example.com';
        $baseUrl = '/base';
        $path = '/redirect-path';
        $expectedUrl = "$scheme://$host$expectedPort$baseUrl$path";
        $request = $this->createRequestObject($requestScheme, $host, $requestPort, $baseUrl);
        $controller = $this->createRedirectController();
        $returnValue = $controller->urlRedirectAction($request, $path, false, $scheme, $httpPort, $httpsPort);
        $this->assertRedirectUrl($returnValue, $expectedUrl);
    }

    public function testUrlRedirectNoScheme()
    {
        $scheme = null;
        $httpPort = 80;
        $httpsPort = 443;
        $requestScheme = 'http';
        $requestPort = 80;
        $expectedPort = '';

        $host = 'www.example.com';
        $baseUrl = '/base';
        $path = '/redirect-path';
        $expectedUrl = "$requestScheme://$host$expectedPort$baseUrl$path";

        $request = $this->createRequestObject($requestScheme, $host, $requestPort, $baseUrl);
        $controller = $this->createRedirectController();
        $returnValue = $controller->urlRedirectAction($request, $path, false, $scheme, $httpPort, $httpsPort);
        $this->assertRedirectUrl($returnValue, $expectedUrl);
    }

    public function pathQueryParamsProvider()
    {
        return array(
            array('http://www.example.com/base/redirect-path', '/redirect-path',  ''),
            array('http://www.example.com/base/redirect-path?foo=bar', '/redirect-path?foo=bar',  ''),
            array('http://www.example.com/base/redirect-path?foo=bar', '/redirect-path', 'foo=bar'),
            array('http://www.example.com/base/redirect-path?foo=bar&abc=example', '/redirect-path?foo=bar', 'abc=example'),
            array('http://www.example.com/base/redirect-path?foo=bar&abc=example&baz=def', '/redirect-path?foo=bar', 'abc=example&baz=def'),
        );
    }
    /**
     * @dataProvider pathQueryParamsProvider
     */
    public function testPathQueryParams($expectedUrl, $path, $queryString)
    {
        $scheme = 'http';
        $host = 'www.example.com';
        $baseUrl = '/base';
        $port = 80;
        $request = $this->createRequestObject($scheme, $host, $port, $baseUrl, $queryString);
        $controller = $this->createRedirectController();
        $returnValue = $controller->urlRedirectAction($request, $path, false, $scheme, $port, null);
        $this->assertRedirectUrl($returnValue, $expectedUrl);
    }

    private function createRequestObject($scheme, $host, $port, $baseUrl, $queryString = '')
    {
        $request = $this->getMock(Request::class);

        $request
            ->expects($this->any())
            ->method('getScheme')
            ->will($this->returnValue($scheme));
        $request
            ->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue($host));
        $request
            ->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue($port));
        $request
            ->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue($baseUrl));
        $request
            ->expects($this->any())
            ->method('getQueryString')
            ->will($this->returnValue($queryString));

        return $request;
    }

    private function createRedirectController($httpPort = null, $httpsPort = null)
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(ConfigInterface::class);

        if (null !== $httpPort) {
            $config
                ->expects($this->once())
                ->method('has')
                ->with($this->equalTo('request.http_port'))
                ->will($this->returnValue(true));
            $config
                ->expects($this->once())
                ->method('get')
                ->with($this->equalTo('request.http_port'))
                ->will($this->returnValue($httpPort));
        }
        if (null !== $httpsPort) {
            $config
                ->expects($this->once())
                ->method('has')
                ->with($this->equalTo('request.https_port'))
                ->will($this->returnValue(true));
            $config
                ->expects($this->once())
                ->method('get')
                ->with($this->equalTo('request.https_port'))
                ->will($this->returnValue($httpsPort));
        }

        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['config', $config]
            ]));

        $controller = new RedirectController();
        $controller->setContainer($container);

        return $controller;
    }
    public function assertRedirectUrl(Response $returnResponse, $expectedUrl)
    {
        $this->assertTrue($returnResponse->isRedirect($expectedUrl), "Expected: $expectedUrl\nGot:      ".$returnResponse->headers->get('Location'));
    }
}

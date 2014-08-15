<?php
namespace Tomahawk\Url\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Routing\Router;
use Tomahawk\Url\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class UrlGeneratorRoutesTest extends TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @var \Tomahawk\Routing\Router
     */
    protected $router;

    public function setup()
    {
        $this->request = Request::create('http://symfony.devbox.com:8182/', 'GET');

        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        $routeCollection = new RouteCollection();
        $this->router = new Router();
        $this->router->setRoutes($routeCollection);
        $this->router->get('/', 'home', 'TestController::get_index');
        $this->router->get('/user/{name}', 'user', 'TestController::get_thing');

        $this->urlGenerator = new UrlGenerator($routeCollection, $this->context);
    }

    public function testRouteNoParams()
    {
        $url = $this->urlGenerator->route('home');
        $this->assertEquals('http://symfony.devbox.com:8182/', $url);
    }

    public function testRouteNeedsParams()
    {
        $this->setExpectedException('Symfony\Component\Routing\Exception\MissingMandatoryParametersException');
        $this->urlGenerator->route('user');
    }

    public function testRouteWithParams()
    {
        $this->setExpectedException('Symfony\Component\Routing\Exception\MissingMandatoryParametersException');
        $url = $this->urlGenerator->route('user', array('tom'));
        $this->assertEquals('http://symfony.devbox.com:8182/user/tom', $url);
    }
}
<?php

namespace Tomahawk\Routing\Tests\Matcher;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Tomahawk\Routing\Matcher\RedirectableUrlMatcher;
use Tomahawk\Routing\Route;
use Tomahawk\Test\TestCase;

class RedirectableUrlMatcherTest extends TestCase
{
    public function testRedirectWithSlash()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo'));
        $matcher = new RedirectableUrlMatcher($coll, $context = new RequestContext());
        $this->assertEquals(array(
            '_controller' => 'Tomahawk\Routing\Controller\RedirectController::urlRedirectAction',
            'path' => '/foo',
            'permanent' => true,
            'scheme' => null,
            'httpPort' => $context->getHttpPort(),
            'httpsPort' => $context->getHttpsPort(),
            '_route' => null,
        ),
            $matcher->match('/foo/')
        );
    }

    public function testRedirectWhenNoSlash()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo'));
        $matcher = new RedirectableUrlMatcher($coll, $context = new RequestContext());
        $this->assertEquals(array(
            '_route' => 'foo',
        ),
            $matcher->match('/foo')
        );
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testRedirectWhenNoSlashThrowsException()
    {
        $coll = new RouteCollection();

        $matcher = new RedirectableUrlMatcher($coll, $context = new RequestContext());

        $matcher->match('/foo');

        $this->fail();
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testRedirectWhenSlashThrowsException()
    {
        $coll = new RouteCollection();

        $matcher = new RedirectableUrlMatcher($coll, $context = new RequestContext());

        $matcher->match('/foo/');

        $this->fail();
    }

    public function testSchemeRedirect()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo', array(), array(), array(), '', array('https')));
        $matcher = new RedirectableUrlMatcher($coll, $context = new RequestContext());
        $this->assertEquals(array(
            '_controller' => 'Tomahawk\Routing\Controller\RedirectController::urlRedirectAction',
            'path' => '/foo',
            'permanent' => true,
            'scheme' => 'https',
            'httpPort' => $context->getHttpPort(),
            'httpsPort' => $context->getHttpsPort(),
            '_route' => 'foo',
        ),
            $matcher->match('/foo')
        );
    }
}

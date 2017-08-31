<?php

namespace Tomahawk\Routing\Tests\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Tomahawk\Routing\Controller\Controller;

class ControllerTest extends TestCase
{
    public function testRenderView()
    {
        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();

        $templating->expects($this->once())->method('render');

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->at(0))->method('get')->will($this->returnValue($templating));

        $controller = $this->getController($container);

        $controller->render('view');
    }

    public function testRenderWithResponse()
    {
        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();
        $templating->expects($this->once())->method('render');

        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();
        $response->expects($this->once())->method('setContent');


        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->at(0))->method('get')->will($this->returnValue($templating));

        $controller = $this->getController($container);

        $controller->render('view', array(), $response);
    }

    public function testRenderWithNoResponse()
    {
        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();
        $templating->expects($this->once())->method('render')->will($this->returnValue('view content'));

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->at(0))->method('get')->will($this->returnValue($templating));

        $controller = $this->getController($container);

        $response = $controller->render('view');

        $this->assertEquals('view content', $response->getContent());
    }

    public function testForward()
    {
        $request = Request::create('/');
        $request->setLocale('fr');
        $request->setRequestFormat('xml');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')->getMock();
        $kernel->expects($this->once())->method('handle')->will($this->returnCallback(function (Request $request) {
            return new Response($request->getRequestFormat().'--'.$request->getLocale());
        }));

        $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->at(0))->method('get')->will($this->returnValue($requestStack));
        $container->expects($this->at(1))->method('get')->will($this->returnValue($kernel));

        $controller = $this->getController($container);

        $response = $controller->forward('a_controller');
        $this->assertEquals('xml--fr', $response->getContent());
    }

    public function testContainerHasGet()
    {
        $container = new Container();
        $container->set('foo', 'bar');
        $controller = $this->getController($container);

        $this->assertTrue($controller->has('foo'));
        $this->assertEquals('bar', $controller->get('foo'));

    }

    /**
     * @param null $container
     * @return Controller
     */
    protected function getController($container = null)
    {
        if (null === $container) {
            $container = $this->getMockBuilder('Tomahawk\DependencyInjection\ContainerInterface')->getMock();
        }

        $controller = new Controller();
        $controller->setContainer($container);

        return $controller;
    }
}

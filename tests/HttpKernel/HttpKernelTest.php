<?php


use Tomahawk\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Tomahawk\HttpKernel\HttpKernel;
use Tomahawk\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpKernelTest extends PHPUnit_Framework_TestCase
{

    public function testThing()
    {

    }

    public function testResourceNotFoundException()
    {

    }

    public function testException()
    {

    }

    public function testRequestEventHasResponse()
    {

    }



    /*public function testHandleWhenControllerThrowsAnExceptionAndRawIsTrue()
    {
        $this->setExpectedException('RuntimeException');

        $kernel = new HttpKernel(new EventDispatcher(), null, $this->getResolver(function () { throw new \RuntimeException(); }));

        $kernel->handle(new Request(), HttpKernelInterface::MASTER_REQUEST, true);
    }


    protected function getResolver($controller = null)
    {
        if (null === $controller) {
            $controller = function () { return new Response('Hello'); };
        }

        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $resolver->expects($this->any())
            ->method('getController')
            ->will($this->returnValue($controller));
        $resolver->expects($this->any())
            ->method('getArguments')
            ->will($this->returnValue(array()));

        return $resolver;
    }*/

}

class TestHttpKernel extends Kernel
{
    public function registerBundles()
    {
        return array();
    }
}

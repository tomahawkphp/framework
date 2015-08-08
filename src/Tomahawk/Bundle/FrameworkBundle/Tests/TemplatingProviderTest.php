<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\Bundle\FrameworkBundle\DI\TemplatingProvider;

class TemplatingProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DI\TemplatingProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

        $templatingProvider = new TemplatingProvider();
        $templatingProvider->register($container);

        $this->assertInstanceOf('Symfony\Component\Templating\EngineInterface', $container->get('templating'));
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('input', $this->getMock('Tomahawk\Input\InputInterface'));
        $container->set('translator', $this->getMock('Symfony\Component\Translation\TranslatorInterface'));
        $container->set('url_generator', $this->getMock('Tomahawk\Url\UrlGeneratorInterface'));
        $container->set('session', $this->getMock('Tomahawk\Session\SessionInterface'));
        $container->set('request_stack', $this->getMock('Symfony\Component\HttpFoundation\RequestStack'));
        $container->set('kernel', $this->getKernel());
        $container->set('config', $this->getConfig());

        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');

        $config->method('get')
            ->will($this->returnValueMap(array(
                array('request.base_url', '', ''),
                array('request.host', 'localhost', 'localhost'),
                array('request.scheme', 'http', 'http'),
                array('request.http_port', 80, 80),
                array('request.https_port', 443, 443),
            )));

        return $config;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(__DIR__ .'/'));


        return $kernel;
    }
}

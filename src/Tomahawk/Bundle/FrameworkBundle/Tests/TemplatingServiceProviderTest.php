<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TemplatingServiceProvider as TemplatingProvider;

class TemplatingServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TemplatingServiceProvider
     */
    public function testProvider()
    {
        $container = $this->getContainer();

        $templatingProvider = new TemplatingProvider();
        $templatingProvider->register($container);

        $this->assertCount(4, $container->findTaggedServiceIds('php.helper'));
        $this->assertCount(2, $container->findTaggedServiceIds('twig.extension'));
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

        $container->set('php_global', new \stdClass());
        $container->tag('php_global', 'php.global');

        if (interface_exists('Twig_FilterInterface')) {

            $twigFilter = $this->getMockBuilder('Twig_SimpleFilter')
                ->disableOriginalConstructor()
                ->setMethods(['getName'])
                ->getMock();
        }
        else {

            $twigFilter = $this->getMockBuilder(\Twig_Filter::class)
                ->disableOriginalConstructor()
                ->getMock();
        }

        $twigFilter->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('twig_filter'));

        $container->set('twig_filter', $twigFilter);
        $container->tag('twig_filter', 'twig.filter');

        $container->set('twig_global', new \stdClass());
        $container->tag('twig_global', 'twig.global');

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
                array('templating.globals', [], ['foo' => 'bar']),
                array('templating.charset', 'UTF-8', 'UTF-8'),
                array('templating.twig.debug', false, false),
                array('templating.twig.auto_reload', false, false),
                array('templating.twig.cache', '', ''),
                array('templating.twig.strict_variables', false, false),
                array('templating.twig.autoescape', 'html', 'html'),
                array('templating.twig.optimizations', -1, -1),
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

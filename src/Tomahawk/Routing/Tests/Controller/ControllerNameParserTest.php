<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The is based on code originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Tests\Controller;

use Tomahawk\Test\TestCase;
use Tomahawk\Routing\Controller\ControllerNameParser;
use Symfony\Component\ClassLoader\ClassLoader;

class ControllerNameParserTest extends TestCase
{
    protected $loader;
    protected function setUp()
    {
        $this->loader = new ClassLoader();
        $this->loader->addPrefixes(array(
            'TestBundle'      => __DIR__.'/../Fixtures',
            'FooBundle'       => __DIR__.'/../Fixtures',
            'TestApplication' => __DIR__.'/../Fixtures',
        ));
        $this->loader->register();
    }
    protected function tearDown()
    {
        spl_autoload_unregister(array($this->loader, 'loadClass'));
        $this->loader = null;
    }

    public function testParse()
    {
        $parser = $this->createParser();
        $this->assertEquals('FooBundle\BarBundle\Controller\BarController::indexAction', $parser->parse('BarBundle:Bar:index'), '->parse() converts a short a:b:c notation string to a class::method string');
        $this->assertEquals('FooBundle\BazBundle\Controller\Test\DefaultController::indexAction', $parser->parse('BazBundle:Test/Default:index'), '->parse() converts a short a:b:c notation string to a class::method string');
        $this->assertEquals('FooBundle\BazBundle\Controller\Test\DefaultController::indexAction', $parser->parse('BazBundle:Test\\Default:index'), '->parse() converts a short a:b:c notation string to a class::method string');
        $this->assertEquals('TestBundle\Controller\HomeController::indexAction', $parser->parse('TestBundle:Home:index'), '->parse() converts a short a:b:c notation string to a class::method string');
        try {
            $parser->parse('foo:');
            $this->fail('->parse() throws an \InvalidArgumentException if the controller is not an a:b:c string');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->parse() throws an \InvalidArgumentException if the controller is not an a:b:c string');
        }

        try {
            $parser->parse('NoBundle:Home:index');
            $this->fail('->parse() throws an \InvalidArgumentException if the bundle does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }

        try {
            $parser->parse('BarBundle:User:index');
            $this->fail('->parse() throws an \InvalidArgumentException if the controller does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    public function testBuild()
    {
        $parser = $this->createParser();
        $this->assertEquals('TestBundle:Home:home', $parser->build('TestBundle\Controller\HomeController::homeAction'), '->parse() converts a class::method string to a short a:b:c notation string');
        $this->assertEquals('BazBundle:Test\Default:index', $parser->build('FooBundle\BazBundle\Controller\Test\DefaultController::indexAction'), '->parse() converts a class::method string to a short a:b:c notation string');
        try {
            $parser->build('TestBundle\FooBundle\Controller\HomeController::index');
            $this->fail('->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }

        try {
            $parser->build('TestBundle\FooBundle\Controller\Default::indexAction');
            $this->fail('->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }

        try {
            $parser->build('Foo\Controller\DefaultController::indexAction');
            $this->fail('->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }
        catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->parse() throws an \InvalidArgumentException if the controller is not an aController::cAction string');
        }
    }


    protected function getKernel()
    {
        $kernel = $this->getMock('Tomahawk\HttpKernel\KernelInterface');

        return $kernel;
    }

    protected function createParser()
    {
        $bundles = array(
            'TestBundle' => array($this->getBundle('TestBundle', 'TestBundle')),
            'BarBundle' => array($this->getBundle('FooBundle\BarBundle', 'BarBundle'), $this->getBundle('FooTestBundle', 'FooTestBundle')),
            'BazBundle' => array($this->getBundle('FooBundle\BazBundle', 'BazBundle')),
        );

        $kernel = $this->getMock('Tomahawk\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnCallback(function ($bundle) use ($bundles) {
                if (!isset($bundles[$bundle])) {
                    throw new \InvalidArgumentException(sprintf('Invalid bundle name "%s"', $bundle));
                }
                return $bundles[$bundle];
            }));

        $bundles = array(
            'TestBundle' => $this->getBundle('TestBundle', 'TestBundle'),
            'BarBundle' => $this->getBundle('FooBundle\BarBundle', 'BarBundle'),
            'BazBundle' => $this->getBundle('FooBundle\BazBundle', 'BazBundle'),
        );

        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue($bundles));

        return new ControllerNameParser($kernel);
    }

    protected function getBundle($namespace, $name)
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getName')->will($this->returnValue($name));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));
        return $bundle;
    }
}
